<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashTransaction::with(['cashAccount', 'currency', 'postedBy']);

        // Filter by account
        if ($request->has('cash_account_id')) {
            $query->where('cash_account_id', $request->cash_account_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'transaction_type' => 'required|in:receipt,payment,transfer_in,transfer_out,adjustment',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'counterparty_type' => 'nullable|string',
            'counterparty_id' => 'nullable|integer',
            'counterparty_name' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,posted,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transactionData = $validator->validated();
            $transactionData['company_id'] = auth()->user()->company_id;
            $transactionData['status'] = $transactionData['status'] ?? 'draft';

            $transaction = CashTransaction::create($transactionData);

            // If transaction is posted, update account balance
            if ($transaction->status === 'posted') {
                $this->updateAccountBalance($transaction);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash transaction created successfully',
                'data' => $transaction->load(['cashAccount', 'currency', 'postedBy'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cash transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = CashTransaction::with(['cashAccount', 'currency', 'postedBy', 'company'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = CashTransaction::findOrFail($id);

        // Can only update draft transactions
        if ($transaction->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Can only update draft transactions'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'transaction_date' => 'date',
            'cash_account_id' => 'exists:cash_accounts,id',
            'transaction_type' => 'in:receipt,payment,transfer_in,transfer_out,adjustment',
            'amount' => 'numeric|min:0',
            'currency_id' => 'exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'counterparty_type' => 'nullable|string',
            'counterparty_id' => 'nullable|integer',
            'counterparty_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cash transaction updated successfully',
                'data' => $transaction->load(['cashAccount', 'currency', 'postedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cash transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $transaction = CashTransaction::findOrFail($id);

            // Can only delete draft transactions
            if ($transaction->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete draft transactions'
                ], 422);
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cash transaction deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post a transaction
     */
    public function post($id)
    {
        try {
            DB::beginTransaction();

            $transaction = CashTransaction::findOrFail($id);

            if ($transaction->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is not in draft status'
                ], 422);
            }

            // Check if account has sufficient balance for payments
            if (in_array($transaction->transaction_type, ['payment', 'transfer_out'])) {
                $account = $transaction->cashAccount;
                if ($account->current_balance < $transaction->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance in cash account'
                    ], 422);
                }
            }

            $transaction->status = 'posted';
            $transaction->posted_by_id = auth()->id();
            $transaction->posted_at = now();
            $transaction->save();

            // Update account balance
            $this->updateAccountBalance($transaction);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction posted successfully',
                'data' => $transaction->load(['cashAccount', 'currency', 'postedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to post transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a transaction
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $transaction = CashTransaction::findOrFail($id);

            if ($transaction->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is already cancelled'
                ], 422);
            }

            // Reverse account balance if transaction was posted
            if ($transaction->status === 'posted') {
                $this->reverseAccountBalance($transaction);
            }

            $transaction->status = 'cancelled';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction cancelled successfully',
                'data' => $transaction->load(['cashAccount', 'currency', 'postedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update account balance based on transaction
     */
    private function updateAccountBalance(CashTransaction $transaction)
    {
        $account = $transaction->cashAccount;
        
        if (in_array($transaction->transaction_type, ['receipt', 'transfer_in'])) {
            $account->current_balance += $transaction->amount;
        } elseif (in_array($transaction->transaction_type, ['payment', 'transfer_out'])) {
            $account->current_balance -= $transaction->amount;
        } elseif ($transaction->transaction_type === 'adjustment') {
            $account->current_balance = $transaction->amount;
        }

        $account->save();
    }

    /**
     * Reverse account balance when cancelling transaction
     */
    private function reverseAccountBalance(CashTransaction $transaction)
    {
        $account = $transaction->cashAccount;
        
        if (in_array($transaction->transaction_type, ['receipt', 'transfer_in'])) {
            $account->current_balance -= $transaction->amount;
        } elseif (in_array($transaction->transaction_type, ['payment', 'transfer_out'])) {
            $account->current_balance += $transaction->amount;
        }

        $account->save();
    }
}
