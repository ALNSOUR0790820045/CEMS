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
     * Validate amount constraints based on transaction type.
     */
    private function validateAmountForTransactionType(Request $request)
    {
        if ($request->has('amount') && $request->has('transaction_type')) {
            $expenseTypes = [PettyCashTransaction::TYPE_EXPENSE, PettyCashTransaction::TYPE_REIMBURSEMENT];
            if (in_array($request->transaction_type, $expenseTypes) && $request->amount < 0) {
                return ['amount' => ['Amount must be positive for expenses and reimbursements']];
            }
        }
        return null;
    }

    /**
     * Update account balance based on transaction type and amount.
     * 
     * Balance update rules:
     * - Expenses: Always decrease balance (use abs to ensure positive amount is subtracted)
     * - Reimbursements: Always increase balance (use abs to ensure positive amount is added)
     * - Adjustments: Can increase or decrease based on sign (preserve original sign)
     * 
     * @param PettyCashAccount $account
     * @param string $transactionType
     * @param float $amount
     * @param bool $reverse If true, reverses the transaction's effect on balance
     */
    private function updateAccountBalance(PettyCashAccount $account, string $transactionType, float $amount, bool $reverse = false)
    {
        $multiplier = $reverse ? -1 : 1;
        
        if ($transactionType === PettyCashTransaction::TYPE_EXPENSE) {
            // Expenses always decrease balance (amount should be positive per validation)
            $account->current_balance -= abs($amount) * $multiplier;
        } elseif ($transactionType === PettyCashTransaction::TYPE_REIMBURSEMENT) {
            // Reimbursements always increase balance (amount should be positive per validation)
            $account->current_balance += abs($amount) * $multiplier;
        } elseif ($transactionType === PettyCashTransaction::TYPE_ADJUSTMENT) {
            // Adjustments can be positive (increase) or negative (decrease) - preserve sign
            $account->current_balance += $amount * $multiplier;
        }
    }

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
        $amountErrors = $this->validateAmountForTransactionType($request);
        if ($amountErrors) {
            return response()->json(['errors' => $amountErrors], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['created_by_id'] = Auth::id();

            $transaction = PettyCashTransaction::create($data);

            // Update account balance
            $account = PettyCashAccount::findOrFail($request->petty_cash_account_id);
            $this->updateAccountBalance($account, $request->transaction_type, $request->amount);
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
        $amountErrors = $this->validateAmountForTransactionType($request);
        if ($amountErrors) {
            return response()->json(['errors' => $amountErrors], 422);
        }

        DB::beginTransaction();
        try {
            // Reverse the old transaction's effect on balance
            $account = $pettyCashTransaction->pettyCashAccount;
            $this->updateAccountBalance($account, $pettyCashTransaction->transaction_type, $pettyCashTransaction->amount, true);

            // Update transaction
            $pettyCashTransaction->update($request->all());

            // Apply the new transaction's effect on balance
            $this->updateAccountBalance($account, $pettyCashTransaction->transaction_type, $pettyCashTransaction->amount);
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
            $this->updateAccountBalance($account, $pettyCashTransaction->transaction_type, $pettyCashTransaction->amount, true);
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
