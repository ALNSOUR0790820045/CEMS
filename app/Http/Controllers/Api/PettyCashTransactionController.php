<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use App\Models\PettyCashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashTransactionController extends Controller
{
    /**
     * Display a listing of petty cash transactions.
     */
    public function index(Request $request)
    {
        $query = PettyCashTransaction::with(['pettyCashAccount', 'company', 'approvedBy', 'createdBy']);

        // Filter by petty cash account
        if ($request->has('petty_cash_account_id')) {
            $query->where('petty_cash_account_id', $request->petty_cash_account_id);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by transaction type
        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->latest('transaction_date')->paginate($request->get('per_page', 15));

        return response()->json($transactions);
    }

    /**
     * Store a newly created petty cash transaction.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'petty_cash_account_id' => 'required|exists:petty_cash_accounts,id',
            'transaction_type' => 'required|in:expense,reimbursement,adjustment',
            'amount' => 'required|numeric',
            'expense_category' => 'nullable|string|max:255',
            'description' => 'required|string',
            'payee' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'attachment_path' => 'nullable|string|max:255',
            'approved_by_id' => 'nullable|exists:users,id',
            'gl_journal_entry_id' => 'nullable|integer',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate amount constraints per transaction type
        if (in_array($request->transaction_type, ['expense', 'reimbursement']) && $request->amount < 0) {
            return response()->json([
                'errors' => ['amount' => ['Amount must be positive for expenses and reimbursements']]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['created_by_id'] = Auth::id();

            $transaction = PettyCashTransaction::create($data);

            // Update account balance
            $account = PettyCashAccount::findOrFail($request->petty_cash_account_id);
            
            if ($request->transaction_type === 'expense') {
                // Expenses always decrease balance (amount should be positive)
                $account->current_balance -= abs($request->amount);
            } elseif ($request->transaction_type === 'reimbursement') {
                // Reimbursements always increase balance (amount should be positive)
                $account->current_balance += abs($request->amount);
            } elseif ($request->transaction_type === 'adjustment') {
                // For adjustments, positive amounts increase balance, negative decrease it
                $account->current_balance += $request->amount;
            }

            $account->save();

            DB::commit();

            $transaction->load(['pettyCashAccount', 'company', 'approvedBy', 'createdBy']);
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create transaction: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified petty cash transaction.
     */
    public function show(PettyCashTransaction $pettyCashTransaction)
    {
        $pettyCashTransaction->load(['pettyCashAccount', 'company', 'approvedBy', 'createdBy']);
        return response()->json($pettyCashTransaction);
    }

    /**
     * Update the specified petty cash transaction.
     */
    public function update(Request $request, PettyCashTransaction $pettyCashTransaction)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'sometimes|required|date',
            'transaction_type' => 'sometimes|required|in:expense,reimbursement,adjustment',
            'amount' => 'sometimes|required|numeric',
            'expense_category' => 'nullable|string|max:255',
            'description' => 'sometimes|required|string',
            'payee' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'attachment_path' => 'nullable|string|max:255',
            'approved_by_id' => 'nullable|exists:users,id',
            'gl_journal_entry_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate amount constraints per transaction type if being updated
        if ($request->has('amount') && $request->has('transaction_type')) {
            if (in_array($request->transaction_type, ['expense', 'reimbursement']) && $request->amount < 0) {
                return response()->json([
                    'errors' => ['amount' => ['Amount must be positive for expenses and reimbursements']]
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Reverse the old transaction's effect on balance
            $account = $pettyCashTransaction->pettyCashAccount;
            
            if ($pettyCashTransaction->transaction_type === 'expense') {
                $account->current_balance += abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'reimbursement') {
                $account->current_balance -= abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'adjustment') {
                $account->current_balance -= $pettyCashTransaction->amount;
            }

            // Update transaction
            $pettyCashTransaction->update($request->all());

            // Apply the new transaction's effect on balance
            if ($pettyCashTransaction->transaction_type === 'expense') {
                $account->current_balance -= abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'reimbursement') {
                $account->current_balance += abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'adjustment') {
                $account->current_balance += $pettyCashTransaction->amount;
            }

            $account->save();

            DB::commit();

            $pettyCashTransaction->load(['pettyCashAccount', 'company', 'approvedBy', 'createdBy']);
            return response()->json($pettyCashTransaction);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update transaction: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified petty cash transaction.
     */
    public function destroy(PettyCashTransaction $pettyCashTransaction)
    {
        DB::beginTransaction();
        try {
            // Reverse the transaction's effect on balance
            $account = $pettyCashTransaction->pettyCashAccount;
            
            if ($pettyCashTransaction->transaction_type === 'expense') {
                $account->current_balance += abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'reimbursement') {
                $account->current_balance -= abs($pettyCashTransaction->amount);
            } elseif ($pettyCashTransaction->transaction_type === 'adjustment') {
                $account->current_balance -= $pettyCashTransaction->amount;
            }

            $account->save();
            $pettyCashTransaction->delete();

            DB::commit();
            return response()->json(['message' => 'Petty cash transaction deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete transaction: ' . $e->getMessage()], 500);
        }
    }
}
