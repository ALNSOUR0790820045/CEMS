<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use App\Models\PettyCashAccount;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PettyCashTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PettyCashTransaction::with([
                'pettyCashAccount', 'expenseCategory', 'costCenter', 
                'project', 'requestedBy', 'approvedBy', 'postedBy'
            ])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('petty_cash_account_id')) {
            $query->where('petty_cash_account_id', $request->petty_cash_account_id);
        }

        if ($request->has('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'petty_cash_account_id' => 'required|exists:petty_cash_accounts,id',
            'transaction_type' => 'required|in:expense,replenishment,adjustment',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'project_id' => 'nullable|exists:projects,id',
            'receipt_number' => 'nullable|string',
            'receipt_date' => 'nullable|date',
            'payee_name' => 'nullable|string',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate business rules
        $account = PettyCashAccount::findOrFail($request->petty_cash_account_id);
        
        if ($request->transaction_type === 'expense') {
            // Check if account has sufficient balance
            if (!$account->hasAvailableBalance($request->amount)) {
                return response()->json([
                    'error' => 'Insufficient balance in petty cash account'
                ], 422);
            }

            // Check if category requires receipt
            if ($request->expense_category_id) {
                $category = ExpenseCategory::find($request->expense_category_id);
                if ($category && $category->requires_receipt && !$request->receipt_number) {
                    return response()->json([
                        'error' => 'Receipt is required for this expense category'
                    ], 422);
                }

                // Check spending limit
                if ($category && $category->spending_limit && $request->amount > $category->spending_limit) {
                    return response()->json([
                        'error' => 'Amount exceeds spending limit for this category'
                    ], 422);
                }
            }
        }

        $transaction = DB::transaction(function () use ($request, $account) {
            // Generate transaction number
            $year = date('Y');
            $lastTransaction = PettyCashTransaction::where('transaction_number', 'like', "PC-{$year}-%")
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastTransaction) {
                $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $transactionNumber = "PC-{$year}-{$newNumber}";

            $transaction = PettyCashTransaction::create([
                'transaction_number' => $transactionNumber,
                'transaction_date' => $request->transaction_date,
                'petty_cash_account_id' => $request->petty_cash_account_id,
                'transaction_type' => $request->transaction_type,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_category_id' => $request->expense_category_id,
                'cost_center_id' => $request->cost_center_id,
                'project_id' => $request->project_id,
                'receipt_number' => $request->receipt_number,
                'receipt_date' => $request->receipt_date,
                'payee_name' => $request->payee_name,
                'status' => 'pending',
                'requested_by_id' => $request->user()->id,
                'attachment_path' => $request->attachment_path,
                'notes' => $request->notes,
                'company_id' => $request->user()->company_id,
            ]);

            return $transaction;
        });

        return response()->json($transaction->load([
            'pettyCashAccount', 'expenseCategory', 'costCenter', 
            'project', 'requestedBy'
        ]), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = PettyCashTransaction::with([
                'pettyCashAccount', 'expenseCategory', 'costCenter', 
                'project', 'requestedBy', 'approvedBy', 'postedBy', 'glJournalEntry'
            ])
            ->findOrFail($id);

        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = PettyCashTransaction::findOrFail($id);

        // Only allow update if status is pending
        if ($transaction->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot update transaction that is not pending'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'project_id' => 'nullable|exists:projects,id',
            'receipt_number' => 'nullable|string',
            'receipt_date' => 'nullable|date',
            'payee_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaction->update($request->all());

        return response()->json($transaction->load([
            'pettyCashAccount', 'expenseCategory', 'costCenter', 
            'project', 'requestedBy'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = PettyCashTransaction::findOrFail($id);

        // Only allow delete if status is pending
        if ($transaction->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot delete transaction that is not pending'
            ], 422);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    /**
     * Approve a transaction.
     */
    public function approve(Request $request, string $id)
    {
        $transaction = PettyCashTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'error' => 'Transaction is not pending approval'
            ], 422);
        }

        DB::transaction(function () use ($transaction, $request) {
            $transaction->update([
                'status' => 'approved',
                'approved_by_id' => $request->user()->id,
                'approved_at' => now(),
            ]);

            // Update account balance for expenses
            if ($transaction->transaction_type === 'expense') {
                $account = $transaction->pettyCashAccount;
                $account->decrement('current_balance', $transaction->amount);
            } elseif ($transaction->transaction_type === 'replenishment') {
                $account = $transaction->pettyCashAccount;
                $account->increment('current_balance', $transaction->amount);
            }
        });

        return response()->json($transaction->load([
            'pettyCashAccount', 'expenseCategory', 'approvedBy'
        ]));
    }

    /**
     * Reject a transaction.
     */
    public function reject(Request $request, string $id)
    {
        $transaction = PettyCashTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'error' => 'Transaction is not pending approval'
            ], 422);
        }

        $transaction->update([
            'status' => 'rejected',
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
            'notes' => ($transaction->notes ?? '') . "\nRejection reason: " . ($request->rejection_reason ?? 'No reason provided'),
        ]);

        return response()->json($transaction->load([
            'pettyCashAccount', 'expenseCategory', 'approvedBy'
        ]));
    }

    /**
     * Post a transaction to GL.
     */
    public function post(Request $request, string $id)
    {
        $transaction = PettyCashTransaction::findOrFail($id);

        if ($transaction->status !== 'approved') {
            return response()->json([
                'error' => 'Transaction must be approved before posting'
            ], 422);
        }

        $transaction->update([
            'status' => 'posted',
            'posted_by_id' => $request->user()->id,
            'posted_at' => now(),
        ]);

        // TODO: Create GL journal entry here if needed

        return response()->json($transaction->load([
            'pettyCashAccount', 'expenseCategory', 'postedBy'
        ]));
    }
}
