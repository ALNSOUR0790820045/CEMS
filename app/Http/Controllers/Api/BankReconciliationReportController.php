<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankReconciliationReportController extends Controller
{
    /**
     * Get bank reconciliation report.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_id' => 'nullable|exists:bank_statements,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankAccount = BankAccount::with('currency')->findOrFail($request->bank_account_id);

        // Build query
        $query = BankStatement::where('bank_account_id', $request->bank_account_id);

        if ($request->has('statement_id')) {
            $query->where('id', $request->statement_id);
        }

        if ($request->has('date_from')) {
            $query->where('statement_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('statement_date', '<=', $request->date_to);
        }

        $statements = $query->with(['lines', 'reconciledBy'])->latest('statement_date')->get();

        // Calculate reconciliation summary
        $totalStatements = $statements->count();
        $reconciledStatements = $statements->where('status', 'reconciled')->count();
        $reconciliationRate = $totalStatements > 0 ? ($reconciledStatements / $totalStatements) * 100 : 0;

        // Get unreconciled items
        $unreconciledLines = BankStatementLine::whereHas('bankStatement', function ($query) use ($request) {
            $query->where('bank_account_id', $request->bank_account_id);
            
            if ($request->has('date_from')) {
                $query->where('statement_date', '>=', $request->date_from);
            }
            
            if ($request->has('date_to')) {
                $query->where('statement_date', '<=', $request->date_to);
            }
        })
        ->where('is_reconciled', false)
        ->with('bankStatement')
        ->get();

        // Calculate totals
        $totalDebit = 0;
        $totalCredit = 0;
        $unreconciledDebit = 0;
        $unreconciledCredit = 0;

        foreach ($statements as $statement) {
            foreach ($statement->lines as $line) {
                $totalDebit += $line->debit_amount;
                $totalCredit += $line->credit_amount;

                if (!$line->is_reconciled) {
                    $unreconciledDebit += $line->debit_amount;
                    $unreconciledCredit += $line->credit_amount;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bank_account' => $bankAccount,
                'summary' => [
                    'total_statements' => $totalStatements,
                    'reconciled_statements' => $reconciledStatements,
                    'reconciliation_rate' => round($reconciliationRate, 2),
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'unreconciled_debit' => $unreconciledDebit,
                    'unreconciled_credit' => $unreconciledCredit,
                    'unreconciled_count' => $unreconciledLines->count(),
                ],
                'statements' => $statements,
                'unreconciled_items' => $unreconciledLines,
            ],
        ]);
    }

    /**
     * Get outstanding items report.
     */
    public function outstandingItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'as_of_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankAccount = BankAccount::with('currency')->findOrFail($request->bank_account_id);
        $asOfDate = $request->as_of_date ?? now()->toDateString();

        // Get unreconciled lines up to the specified date
        $outstandingLines = BankStatementLine::whereHas('bankStatement', function ($query) use ($request, $asOfDate) {
            $query->where('bank_account_id', $request->bank_account_id)
                  ->where('statement_date', '<=', $asOfDate);
        })
        ->where('is_reconciled', false)
        ->with(['bankStatement'])
        ->orderBy('transaction_date')
        ->get();

        // Calculate totals
        $totalDebit = $outstandingLines->sum('debit_amount');
        $totalCredit = $outstandingLines->sum('credit_amount');
        $netAmount = $totalCredit - $totalDebit;

        return response()->json([
            'success' => true,
            'data' => [
                'bank_account' => $bankAccount,
                'as_of_date' => $asOfDate,
                'outstanding_items' => $outstandingLines,
                'summary' => [
                    'total_items' => $outstandingLines->count(),
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'net_amount' => $netAmount,
                ],
            ],
        ]);
    }

    /**
     * Get bank book report.
     */
    public function bankBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankAccount = BankAccount::with('currency')->findOrFail($request->bank_account_id);

        // Get all lines within the date range
        $lines = BankStatementLine::whereHas('bankStatement', function ($query) use ($request) {
            $query->where('bank_account_id', $request->bank_account_id)
                  ->whereBetween('statement_date', [$request->date_from, $request->date_to]);
        })
        ->with(['bankStatement'])
        ->orderBy('transaction_date')
        ->get();

        // Calculate running balance
        $openingBalance = $bankAccount->current_balance;
        $runningBalance = $openingBalance;
        $linesWithBalance = [];

        foreach ($lines as $line) {
            $runningBalance += ($line->credit_amount - $line->debit_amount);
            $linesWithBalance[] = [
                'id' => $line->id,
                'transaction_date' => $line->transaction_date,
                'description' => $line->description,
                'reference_number' => $line->reference_number,
                'debit_amount' => $line->debit_amount,
                'credit_amount' => $line->credit_amount,
                'running_balance' => $runningBalance,
                'is_reconciled' => $line->is_reconciled,
            ];
        }

        $totalDebit = $lines->sum('debit_amount');
        $totalCredit = $lines->sum('credit_amount');
        $closingBalance = $runningBalance;

        return response()->json([
            'success' => true,
            'data' => [
                'bank_account' => $bankAccount,
                'period' => [
                    'from' => $request->date_from,
                    'to' => $request->date_to,
                ],
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'transactions' => $linesWithBalance,
                'summary' => [
                    'total_transactions' => $lines->count(),
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'net_movement' => $totalCredit - $totalDebit,
                ],
            ],
        ]);
    }
}
