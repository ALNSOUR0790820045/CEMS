<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashTransaction::with(['cashAccount', 'company', 'createdBy'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('cash_account_id')) {
            $query->where('cash_account_id', $request->cash_account_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->latest('transaction_date')->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:receipt,payment,transfer',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'payee_payer' => 'nullable|string|max:255',
            'description' => 'required|string',
            'related_document_type' => 'nullable|string|max:255',
            'related_document_id' => 'nullable|integer',
            'status' => 'nullable|in:draft,posted,cancelled',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'draft';

        $transaction = CashTransaction::create($validated);
        $transaction->load(['cashAccount', 'company', 'createdBy']);

        // Update cash account balance if posted
        if ($transaction->status === 'posted') {
            $this->updateCashAccountBalance($transaction);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cash transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CashTransaction $cashTransaction)
    {
        if ($cashTransaction->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $cashTransaction->load(['cashAccount', 'company', 'createdBy', 'glJournalEntry']);

        return response()->json([
            'success' => true,
            'data' => $cashTransaction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashTransaction $cashTransaction)
    {
        if ($cashTransaction->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Prevent updating posted transactions
        if ($cashTransaction->status === 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update posted transactions',
            ], 422);
        }

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:receipt,payment,transfer',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'payee_payer' => 'nullable|string|max:255',
            'description' => 'required|string',
            'related_document_type' => 'nullable|string|max:255',
            'related_document_id' => 'nullable|integer',
            'status' => 'nullable|in:draft,posted,cancelled',
        ]);

        $cashTransaction->update($validated);
        $cashTransaction->load(['cashAccount', 'company', 'createdBy']);

        // Update cash account balance if status changed to posted
        if (isset($validated['status']) && $validated['status'] === 'posted') {
            $this->updateCashAccountBalance($cashTransaction);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cash transaction updated successfully',
            'data' => $cashTransaction,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashTransaction $cashTransaction)
    {
        if ($cashTransaction->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Prevent deleting posted transactions
        if ($cashTransaction->status === 'posted') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete posted transactions. Cancel it first.',
            ], 422);
        }

        $cashTransaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cash transaction deleted successfully',
        ]);
    }

    /**
     * Create a receipt transaction
     */
    public function receipt(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'payee_payer' => 'nullable|string|max:255',
            'description' => 'required|string',
            'related_document_type' => 'nullable|string|max:255',
            'related_document_id' => 'nullable|integer',
        ]);

        $validated['transaction_type'] = 'receipt';
        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by_id'] = Auth::id();
        $validated['status'] = 'posted';

        DB::beginTransaction();
        try {
            $transaction = CashTransaction::create($validated);
            $this->updateCashAccountBalance($transaction);

            DB::commit();

            $transaction->load(['cashAccount', 'company', 'createdBy']);

            return response()->json([
                'success' => true,
                'message' => 'Receipt transaction created successfully',
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create receipt transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a payment transaction
     */
    public function payment(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'payee_payer' => 'nullable|string|max:255',
            'description' => 'required|string',
            'related_document_type' => 'nullable|string|max:255',
            'related_document_id' => 'nullable|integer',
        ]);

        $validated['transaction_type'] = 'payment';
        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by_id'] = Auth::id();
        $validated['status'] = 'posted';

        DB::beginTransaction();
        try {
            // Check if account has sufficient balance
            $cashAccount = CashAccount::findOrFail($validated['cash_account_id']);
            if ($cashAccount->current_balance < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in cash account',
                ], 422);
            }

            $transaction = CashTransaction::create($validated);
            $this->updateCashAccountBalance($transaction, $cashAccount);

            DB::commit();

            $transaction->load(['cashAccount', 'company', 'createdBy']);

            return response()->json([
                'success' => true,
                'message' => 'Payment transaction created successfully',
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a transfer transaction
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'from_account_id' => 'required|exists:cash_accounts,id',
            'to_account_id' => 'required|exists:cash_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Check if from account has sufficient balance
            $fromAccount = CashAccount::findOrFail($validated['from_account_id']);
            $toAccount = CashAccount::findOrFail($validated['to_account_id']);

            if ($fromAccount->current_balance < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in source account',
                ], 422);
            }

            // Create payment transaction for from account
            $paymentTransaction = CashTransaction::create([
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => 'payment',
                'cash_account_id' => $validated['from_account_id'],
                'amount' => $validated['amount'],
                'payment_method' => 'bank_transfer',
                'reference_number' => $validated['reference_number'] ?? null,
                'payee_payer' => 'Transfer Out',
                'description' => $validated['description'],
                'related_document_type' => 'transfer',
                'status' => 'posted',
                'company_id' => Auth::user()->company_id,
                'created_by_id' => Auth::id(),
            ]);

            // Create receipt transaction for to account
            $receiptTransaction = CashTransaction::create([
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => 'receipt',
                'cash_account_id' => $validated['to_account_id'],
                'amount' => $validated['amount'],
                'payment_method' => 'bank_transfer',
                'reference_number' => $validated['reference_number'] ?? null,
                'payee_payer' => 'Transfer In',
                'description' => $validated['description'],
                'related_document_type' => 'transfer',
                'related_document_id' => $paymentTransaction->id,
                'status' => 'posted',
                'company_id' => Auth::user()->company_id,
                'created_by_id' => Auth::id(),
            ]);

            // Link the transactions
            $paymentTransaction->update(['related_document_id' => $receiptTransaction->id]);

            // Update balances with already-fetched accounts
            $this->updateCashAccountBalance($paymentTransaction, $fromAccount);
            $this->updateCashAccountBalance($receiptTransaction, $toAccount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer transaction created successfully',
                'data' => [
                    'payment' => $paymentTransaction->load(['cashAccount', 'company', 'createdBy']),
                    'receipt' => $receiptTransaction->load(['cashAccount', 'company', 'createdBy']),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create transfer transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cash account balance based on transaction
     */
    private function updateCashAccountBalance(CashTransaction $transaction, ?CashAccount $cashAccount = null)
    {
        if (! $cashAccount) {
            $cashAccount = CashAccount::findOrFail($transaction->cash_account_id);
        }

        if ($transaction->transaction_type === 'receipt') {
            $cashAccount->current_balance += $transaction->amount;
        } elseif ($transaction->transaction_type === 'payment') {
            $cashAccount->current_balance -= $transaction->amount;
        }

        $cashAccount->save();
    }
}
