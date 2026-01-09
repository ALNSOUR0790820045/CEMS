<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\CashForecast;
use App\Models\DailyCashPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashReportController extends Controller
{
    public function dailyPositions(Request $request)
    {
        $query = DailyCashPosition::with(['cashAccount', 'reconciledBy']);

        if ($request->has('cash_account_id')) {
            $query->where('cash_account_id', $request->cash_account_id);
        }

        if ($request->has('from_date')) {
            $query->where('position_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('position_date', '<=', $request->to_date);
        }

        if ($request->has('is_reconciled')) {
            $query->where('is_reconciled', $request->is_reconciled);
        }

        $positions = $query->orderBy('position_date', 'desc')->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $positions
        ]);
    }

    public function reconcile(Request $request)
    {
        $request->validate([
            'position_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $position = DailyCashPosition::where('position_date', $request->position_date)
                ->where('cash_account_id', $request->cash_account_id)
                ->firstOrFail();

            $position->is_reconciled = true;
            $position->reconciled_by_id = auth()->id();
            $position->reconciled_at = now();
            $position->notes = $request->notes;
            $position->save();

            return response()->json([
                'success' => true,
                'message' => 'Position reconciled successfully',
                'data' => $position
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reconcile position',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cashFlowStatement(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());

        $receipts = CashTransaction::posted()
            ->whereIn('transaction_type', ['receipt', 'transfer_in'])
            ->dateRange($fromDate, $toDate)
            ->sum('amount');

        $payments = CashTransaction::posted()
            ->whereIn('transaction_type', ['payment', 'transfer_out'])
            ->dateRange($fromDate, $toDate)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $fromDate,
                    'to' => $toDate
                ],
                'total_receipts' => $receipts,
                'total_payments' => $payments,
                'net_cash_flow' => $receipts - $payments
            ]
        ]);
    }

    public function cashPosition(Request $request)
    {
        $accounts = CashAccount::active()
            ->with(['currency', 'branch'])
            ->get();

        $totalBalance = 0;
        $accountBalances = [];

        foreach ($accounts as $account) {
            $balance = $account->current_balance;
            $totalBalance += $balance;
            
            $accountBalances[] = [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'currency' => $account->currency->code ?? null,
                'current_balance' => $balance
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'as_of_date' => now()->toDateString(),
                'total_balance' => $totalBalance,
                'accounts' => $accountBalances
            ]
        ]);
    }

    public function cashForecast(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->addMonths(3)->endOfMonth());

        $forecasts = CashForecast::dateRange($fromDate, $toDate)
            ->orderBy('forecast_date')
            ->get();

        $summary = [
            'total_expected_inflows' => $forecasts->where('forecast_type', 'inflow')->sum('expected_amount'),
            'total_expected_outflows' => $forecasts->where('forecast_type', 'outflow')->sum('expected_amount'),
        ];

        $summary['net_expected_cash_flow'] = $summary['total_expected_inflows'] - $summary['total_expected_outflows'];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $fromDate,
                    'to' => $toDate
                ],
                'summary' => $summary,
                'forecasts' => $forecasts
            ]
        ]);
    }

    public function cashMovement(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());
        $accountId = $request->input('cash_account_id');

        $query = CashTransaction::with(['cashAccount', 'currency'])
            ->posted()
            ->dateRange($fromDate, $toDate);

        if ($accountId) {
            $query->where('cash_account_id', $accountId);
        }

        $transactions = $query->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        $summary = [
            'total_receipts' => $transactions->whereIn('transaction_type', ['receipt', 'transfer_in'])->sum('amount'),
            'total_payments' => $transactions->whereIn('transaction_type', ['payment', 'transfer_out'])->sum('amount'),
        ];

        $summary['net_movement'] = $summary['total_receipts'] - $summary['total_payments'];

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $fromDate,
                    'to' => $toDate
                ],
                'summary' => $summary,
                'transactions' => $transactions
            ]
        ]);
    }
}
