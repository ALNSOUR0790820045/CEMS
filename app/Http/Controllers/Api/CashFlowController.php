<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    /**
     * Get cash flow forecast
     */
    public function forecast(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
        ]);

        $fromDate = $validated['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $validated['to_date'] ?? now()->endOfMonth()->toDateString();

        $query = CashTransaction::where('company_id', Auth::user()->company_id)
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$fromDate, $toDate]);

        if (isset($validated['cash_account_id'])) {
            $query->where('cash_account_id', $validated['cash_account_id']);
        }

        // Get receipts and payments
        $transactions = $query->get();

        $receipts = $transactions->where('transaction_type', 'receipt');
        $payments = $transactions->where('transaction_type', 'payment');

        // Calculate totals
        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netCashFlow = $totalReceipts - $totalPayments;

        // Get current cash balance
        $cashAccountsQuery = CashAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true);

        if (isset($validated['cash_account_id'])) {
            $cashAccountsQuery->where('id', $validated['cash_account_id']);
        }

        $currentBalance = $cashAccountsQuery->sum('current_balance');

        // Group by date for daily breakdown
        $dailyBreakdown = [];
        $runningBalance = $currentBalance - $netCashFlow; // Start with balance at beginning of period

        foreach ($transactions->groupBy(function($transaction) {
            return $transaction->transaction_date->format('Y-m-d');
        }) as $date => $dayTransactions) {
            $dayReceipts = $dayTransactions->where('transaction_type', 'receipt')->sum('amount');
            $dayPayments = $dayTransactions->where('transaction_type', 'payment')->sum('amount');
            $dayNet = $dayReceipts - $dayPayments;
            $runningBalance += $dayNet;

            $dailyBreakdown[] = [
                'date' => $date,
                'receipts' => $dayReceipts,
                'payments' => $dayPayments,
                'net_cash_flow' => $dayNet,
                'balance' => $runningBalance,
            ];
        }

        // Group by payment method
        $receiptsByMethod = $receipts->groupBy('payment_method')->map(function($group) {
            return $group->sum('amount');
        });

        $paymentsByMethod = $payments->groupBy('payment_method')->map(function($group) {
            return $group->sum('amount');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                ],
                'summary' => [
                    'opening_balance' => $currentBalance - $netCashFlow,
                    'total_receipts' => $totalReceipts,
                    'total_payments' => $totalPayments,
                    'net_cash_flow' => $netCashFlow,
                    'closing_balance' => $currentBalance,
                ],
                'receipts_by_method' => $receiptsByMethod,
                'payments_by_method' => $paymentsByMethod,
                'daily_breakdown' => $dailyBreakdown,
            ],
        ]);
    }

    /**
     * Get cash account summary
     */
    public function summary(Request $request)
    {
        $cashAccounts = CashAccount::with('currency')
            ->where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $summary = [
            'total_balance' => 0,
            'accounts_by_type' => [],
            'accounts' => [],
        ];

        foreach ($cashAccounts as $account) {
            $summary['total_balance'] += $account->current_balance;

            if (!isset($summary['accounts_by_type'][$account->account_type])) {
                $summary['accounts_by_type'][$account->account_type] = [
                    'count' => 0,
                    'balance' => 0,
                ];
            }

            $summary['accounts_by_type'][$account->account_type]['count']++;
            $summary['accounts_by_type'][$account->account_type]['balance'] += $account->current_balance;

            $summary['accounts'][] = [
                'id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'balance' => $account->current_balance,
                'currency' => $account->currency->code,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
