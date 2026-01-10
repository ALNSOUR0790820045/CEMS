<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankReconciliation;
use App\Models\ReconciliationItem;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankReportController extends Controller
{
    /**
     * Generate bank reconciliation report.
     */
    public function reconciliationReport(Request $request): JsonResponse
    {
        $request->validate([
            'reconciliation_id' => 'required|exists:bank_reconciliations,id',
        ]);

        $reconciliation = BankReconciliation::with([
            'bankAccount',
            'items',
            'preparedBy',
            'approvedBy',
        ])->findOrFail($request->reconciliation_id);

        $report = [
            'reconciliation' => $reconciliation,
            'book_balance' => $reconciliation->book_balance,
            'bank_balance' => $reconciliation->bank_balance,
            'adjustments' => [
                'outstanding_checks' => $reconciliation->items()
                    ->where('item_type', 'outstanding_check')
                    ->sum('amount'),
                'deposits_in_transit' => $reconciliation->items()
                    ->where('item_type', 'deposit_in_transit')
                    ->sum('amount'),
                'bank_charges' => $reconciliation->items()
                    ->where('item_type', 'bank_charge')
                    ->sum('amount'),
                'bank_interest' => $reconciliation->items()
                    ->where('item_type', 'bank_interest')
                    ->sum('amount'),
            ],
            'adjusted_book_balance' => $reconciliation->adjusted_book_balance,
            'adjusted_bank_balance' => $reconciliation->adjusted_bank_balance,
            'difference' => $reconciliation->difference,
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get outstanding checks report.
     */
    public function outstandingChecks(Request $request): JsonResponse
    {
        $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'as_of_date' => 'nullable|date',
        ]);

        $query = ReconciliationItem::with(['bankReconciliation.bankAccount'])
            ->where('item_type', 'outstanding_check')
            ->where('is_cleared', false);

        if ($request->has('bank_account_id')) {
            $query->whereHas('bankReconciliation', function ($q) use ($request) {
                $q->where('bank_account_id', $request->bank_account_id);
            });
        }

        if ($request->has('as_of_date')) {
            $query->where('transaction_date', '<=', $request->as_of_date);
        }

        $outstandingChecks = $query->get();
        $totalAmount = $outstandingChecks->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $outstandingChecks,
                'total_amount' => $totalAmount,
                'count' => $outstandingChecks->count(),
            ],
        ]);
    }

    /**
     * Get deposits in transit report.
     */
    public function depositsInTransit(Request $request): JsonResponse
    {
        $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'as_of_date' => 'nullable|date',
        ]);

        $query = ReconciliationItem::with(['bankReconciliation.bankAccount'])
            ->where('item_type', 'deposit_in_transit')
            ->where('is_cleared', false);

        if ($request->has('bank_account_id')) {
            $query->whereHas('bankReconciliation', function ($q) use ($request) {
                $q->where('bank_account_id', $request->bank_account_id);
            });
        }

        if ($request->has('as_of_date')) {
            $query->where('transaction_date', '<=', $request->as_of_date);
        }

        $depositsInTransit = $query->get();
        $totalAmount = $depositsInTransit->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $depositsInTransit,
                'total_amount' => $totalAmount,
                'count' => $depositsInTransit->count(),
            ],
        ]);
    }

    /**
     * Generate bank book report.
     */
    public function bankBook(Request $request): JsonResponse
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $bankAccount = BankAccount::with(['currency', 'company'])
            ->findOrFail($request->bank_account_id);

        // Get transactions (assuming you have a Transaction model)
        $transactions = $bankAccount->transactions()
            ->whereBetween('transaction_date', [$request->from_date, $request->to_date])
            ->orderBy('transaction_date')
            ->get();

        $openingBalance = $bankAccount->transactions()
            ->where('transaction_date', '<', $request->from_date)
            ->sum('amount');

        $report = [
            'bank_account' => $bankAccount,
            'period' => [
                'from' => $request->from_date,
                'to' => $request->to_date,
            ],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'closing_balance' => $openingBalance + $transactions->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}
