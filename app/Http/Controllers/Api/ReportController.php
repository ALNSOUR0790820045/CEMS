<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Get Trial Balance Report
     */
    public function trialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $companyId = $request->input('company_id');
        $departmentId = $request->input('department_id');

        $query = Account::query()
            ->with('department')
            ->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $accounts = $query->get()->map(function ($account) use ($dateFrom, $dateTo) {
            $transactions = Transaction::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                        ->whereBetween('entry_date', [$dateFrom, $dateTo]);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            $debit = (float) ($transactions->total_debit ?? 0);
            $credit = (float) ($transactions->total_credit ?? 0);
            $balance = $debit - $credit;

            return [
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->type,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'debit_formatted' => number_format($debit, 2),
                'credit_formatted' => number_format($credit, 2),
                'balance_formatted' => number_format($balance, 2),
            ];
        })->filter(function ($item) {
            return $item['debit'] != 0 || $item['credit'] != 0;
        })->values();

        $totalDebit = $accounts->sum('debit');
        $totalCredit = $accounts->sum('credit');

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'accounts' => $accounts,
                'totals' => [
                    'debit' => number_format($totalDebit, 2),
                    'credit' => number_format($totalCredit, 2),
                    'difference' => number_format($totalDebit - $totalCredit, 2),
                ],
            ],
        ]);
    }

    /**
     * Get Balance Sheet Report
     */
    public function balanceSheet(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $date = $request->input('date', Carbon::now());
        $companyId = $request->input('company_id');

        $query = Account::query()->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->get();

        $assets = $this->calculateAccountTypeBalance($accounts->where('type', 'asset'), $date);
        $liabilities = $this->calculateAccountTypeBalance($accounts->where('type', 'liability'), $date);
        $equity = $this->calculateAccountTypeBalance($accounts->where('type', 'equity'), $date);

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'assets' => [
                    'current' => $assets['current'],
                    'non_current' => $assets['non_current'],
                    'total' => $assets['total'],
                ],
                'liabilities' => [
                    'current' => $liabilities['current'],
                    'non_current' => $liabilities['non_current'],
                    'total' => $liabilities['total'],
                ],
                'equity' => [
                    'total' => $equity['total'],
                ],
                'total_liabilities_and_equity' => number_format($liabilities['total'] + $equity['total'], 2),
            ],
        ]);
    }

    /**
     * Get Income Statement (P&L) Report
     */
    public function incomeStatement(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $companyId = $request->input('company_id');

        $query = Account::query()->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->get();

        $revenue = $this->calculateAccountTypeBalance(
            $accounts->where('type', 'revenue'),
            $dateTo,
            $dateFrom
        );

        $expenses = $this->calculateAccountTypeBalance(
            $accounts->where('type', 'expense'),
            $dateTo,
            $dateFrom
        );

        $netIncome = $revenue['total'] - $expenses['total'];

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'revenue' => [
                    'operating' => $revenue['operating'] ?? 0,
                    'non_operating' => $revenue['non_operating'] ?? 0,
                    'total' => $revenue['total'],
                ],
                'expenses' => [
                    'operating' => $expenses['operating'] ?? 0,
                    'non_operating' => $expenses['non_operating'] ?? 0,
                    'total' => $expenses['total'],
                ],
                'net_income' => number_format($netIncome, 2),
            ],
        ]);
    }

    /**
     * Get Cash Flow Statement
     */
    public function cashFlow(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'operating_activities' => [
                    'cash_from_operations' => 0,
                    'cash_paid_to_suppliers' => 0,
                    'net_cash_from_operating' => 0,
                ],
                'investing_activities' => [
                    'purchase_of_assets' => 0,
                    'sale_of_assets' => 0,
                    'net_cash_from_investing' => 0,
                ],
                'financing_activities' => [
                    'loans_received' => 0,
                    'loans_repaid' => 0,
                    'net_cash_from_financing' => 0,
                ],
                'net_increase_in_cash' => 0,
                'cash_at_beginning' => 0,
                'cash_at_end' => 0,
            ],
        ]);
    }

    /**
     * Get General Ledger Report
     */
    public function generalLedger(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $accountId = $request->input('account_id');

        $query = Transaction::query()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]);
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $transactions = $query->orderBy('id')->get()->map(function ($transaction) {
            return [
                'date' => $transaction->journalEntry->entry_date,
                'entry_number' => $transaction->journalEntry->entry_number,
                'account' => $transaction->account->name,
                'description' => $transaction->description ?? $transaction->journalEntry->description,
                'debit' => number_format($transaction->debit, 2),
                'credit' => number_format($transaction->credit, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'transactions' => $transactions,
            ],
        ]);
    }

    /**
     * Get Account Statement
     */
    public function accountStatement(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $accountId = $request->input('account_id');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        $account = Account::findOrFail($accountId);

        $transactions = Transaction::where('account_id', $accountId)
            ->with('journalEntry')
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]);
            })
            ->orderBy('id')
            ->get();

        $balance = 0;
        $statement = $transactions->map(function ($transaction) use (&$balance) {
            $balance += $transaction->debit - $transaction->credit;

            return [
                'date' => $transaction->journalEntry->entry_date,
                'entry_number' => $transaction->journalEntry->entry_number,
                'description' => $transaction->description ?? $transaction->journalEntry->description,
                'debit' => number_format($transaction->debit, 2),
                'credit' => number_format($transaction->credit, 2),
                'balance' => number_format($balance, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'account' => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                ],
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'statement' => $statement,
                'ending_balance' => number_format($balance, 2),
            ],
        ]);
    }

    /**
     * Get Executive Dashboard
     */
    public function executiveDashboard(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_revenue' => 0,
                    'total_expenses' => 0,
                    'net_income' => 0,
                    'total_assets' => 0,
                    'total_liabilities' => 0,
                    'equity' => 0,
                ],
                'trends' => [],
                'kpis' => [],
            ],
        ]);
    }

    /**
     * Get KPI Metrics
     */
    public function kpiMetrics(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'current_ratio' => 0,
                'quick_ratio' => 0,
                'debt_to_equity' => 0,
                'return_on_assets' => 0,
                'return_on_equity' => 0,
                'gross_profit_margin' => 0,
                'net_profit_margin' => 0,
            ],
        ]);
    }

    /**
     * Get Revenue Analysis
     */
    public function revenueAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'group_by' => 'nullable|in:day,week,month,quarter,year',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => 0,
                'breakdown' => [],
                'trend' => [],
            ],
        ]);
    }

    /**
     * Get Expense Analysis
     */
    public function expenseAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'group_by' => 'nullable|in:day,week,month,quarter,year',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_expenses' => 0,
                'breakdown' => [],
                'trend' => [],
            ],
        ]);
    }

    /**
     * Get Profitability Analysis
     */
    public function profitabilityAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'dimension' => 'nullable|in:project,department',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'by_project' => [],
                'by_department' => [],
                'overall' => [
                    'gross_profit' => 0,
                    'net_profit' => 0,
                    'margin' => 0,
                ],
            ],
        ]);
    }

    /**
     * Get VAT Report
     */
    public function vatReport(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'output_vat' => 0,
                'input_vat' => 0,
                'net_vat' => 0,
                'transactions' => [],
            ],
        ]);
    }

    /**
     * Get Withholding Tax Report
     */
    public function withholdingTaxReport(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_withheld' => 0,
                'transactions' => [],
            ],
        ]);
    }

    /**
     * Get Audit Trail
     */
    public function auditTrail(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        $entries = JournalEntry::with(['creator', 'approver'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'entry_number' => $entry->entry_number,
                    'date' => $entry->entry_date,
                    'type' => $entry->type,
                    'status' => $entry->status,
                    'created_by' => $entry->creator->name ?? null,
                    'approved_by' => $entry->approver->name ?? null,
                    'created_at' => $entry->created_at,
                    'approved_at' => $entry->approved_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'entries' => $entries,
            ],
        ]);
    }

    /**
     * Helper: Calculate balance for account types
     */
    private function calculateAccountTypeBalance($accounts, $dateTo, $dateFrom = null)
    {
        $result = [
            'current' => 0,
            'non_current' => 0,
            'operating' => 0,
            'non_operating' => 0,
            'total' => 0,
        ];

        foreach ($accounts as $account) {
            $query = Transaction::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($dateTo, $dateFrom) {
                    $q->where('status', 'posted')
                        ->where('entry_date', '<=', $dateTo);
                    if ($dateFrom) {
                        $q->where('entry_date', '>=', $dateFrom);
                    }
                })
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->first();

            $balance = abs($query->balance ?? 0);

            if ($account->category) {
                $result[$account->category] += $balance;
            }

            $result['total'] += $balance;
        }

        foreach ($result as $key => $value) {
            $result[$key] = number_format($value, 2);
        }

        return $result;
    }
}
