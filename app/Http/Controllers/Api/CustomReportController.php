<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;

class CustomReportController extends Controller
{
    /**
     * Generate Custom Report
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:transaction,account,summary',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'period' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
            'account_range' => 'nullable|array',
            'account_range.*' => 'exists:accounts,id',
            'account_type' => 'nullable|in:asset,liability,equity,revenue,expense',
            'currency' => 'nullable|string|size:3',
            'group_by' => 'nullable|array',
            'group_by.*' => 'in:account,project,department,date',
            'filters' => 'nullable|array',
            'export_format' => 'nullable|in:json,pdf,excel',
        ]);

        $reportType = $request->input('report_type');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $period = $request->input('period', 'monthly');
        $companyId = $request->input('company_id');
        $projectId = $request->input('project_id');
        $departmentId = $request->input('department_id');
        $accountRange = $request->input('account_range');
        $accountType = $request->input('account_type');
        $currency = $request->input('currency', 'SAR');
        $groupBy = $request->input('group_by', []);
        $exportFormat = $request->input('export_format', 'json');

        // Build query based on report type
        $data = match($reportType) {
            'transaction' => $this->generateTransactionReport(
                $dateFrom, 
                $dateTo, 
                $companyId, 
                $projectId, 
                $departmentId, 
                $accountRange,
                $groupBy
            ),
            'account' => $this->generateAccountReport(
                $dateFrom, 
                $dateTo, 
                $companyId, 
                $accountType, 
                $accountRange
            ),
            'summary' => $this->generateSummaryReport(
                $dateFrom, 
                $dateTo, 
                $companyId, 
                $period
            ),
            default => []
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                'report_type' => $reportType,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'filters' => [
                    'company_id' => $companyId,
                    'project_id' => $projectId,
                    'department_id' => $departmentId,
                    'account_type' => $accountType,
                    'currency' => $currency,
                ],
                'results' => $data,
                'export_format' => $exportFormat,
            ],
        ]);
    }

    /**
     * Generate Transaction Report
     */
    private function generateTransactionReport(
        $dateFrom, 
        $dateTo, 
        $companyId = null, 
        $projectId = null, 
        $departmentId = null,
        $accountRange = null,
        $groupBy = []
    ): array {
        $query = Transaction::with(['account', 'journalEntry', 'project', 'department'])
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo, $companyId) {
                $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]);
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
            });

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($accountRange) {
            $query->whereIn('account_id', $accountRange);
        }

        $transactions = $query->get();

        // Group by if specified
        if (!empty($groupBy)) {
            $grouped = [];
            foreach ($transactions as $transaction) {
                $key = $this->buildGroupKey($transaction, $groupBy);
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'group_key' => $key,
                        'total_debit' => 0,
                        'total_credit' => 0,
                        'count' => 0,
                    ];
                }
                $grouped[$key]['total_debit'] += $transaction->debit;
                $grouped[$key]['total_credit'] += $transaction->credit;
                $grouped[$key]['count']++;
            }
            return array_values($grouped);
        }

        return $transactions->map(function ($transaction) {
            return [
                'date' => $transaction->journalEntry->entry_date,
                'entry_number' => $transaction->journalEntry->entry_number,
                'account' => $transaction->account->name,
                'project' => $transaction->project->name ?? null,
                'department' => $transaction->department->name ?? null,
                'description' => $transaction->description,
                'debit' => number_format($transaction->debit, 2),
                'credit' => number_format($transaction->credit, 2),
            ];
        })->toArray();
    }

    /**
     * Generate Account Report
     */
    private function generateAccountReport(
        $dateFrom, 
        $dateTo, 
        $companyId = null, 
        $accountType = null,
        $accountRange = null
    ): array {
        $query = Account::with('department')->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($accountType) {
            $query->where('type', $accountType);
        }

        if ($accountRange) {
            $query->whereIn('id', $accountRange);
        }

        $accounts = $query->get();

        return $accounts->map(function ($account) use ($dateFrom, $dateTo) {
            $transactions = Transaction::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                        ->whereBetween('entry_date', [$dateFrom, $dateTo]);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            return [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'category' => $account->category,
                'department' => $account->department->name ?? null,
                'debit' => number_format($transactions->total_debit ?? 0, 2),
                'credit' => number_format($transactions->total_credit ?? 0, 2),
                'balance' => number_format(($transactions->total_debit ?? 0) - ($transactions->total_credit ?? 0), 2),
            ];
        })->toArray();
    }

    /**
     * Generate Summary Report
     */
    private function generateSummaryReport(
        $dateFrom, 
        $dateTo, 
        $companyId = null,
        $period = 'monthly'
    ): array {
        // Generate summary data grouped by period
        return [
            'total_revenue' => 0,
            'total_expenses' => 0,
            'net_income' => 0,
            'by_period' => [],
        ];
    }

    /**
     * Build group key for grouping transactions
     */
    private function buildGroupKey($transaction, array $groupBy): string
    {
        $parts = [];
        
        foreach ($groupBy as $field) {
            $parts[] = match($field) {
                'account' => $transaction->account->code,
                'project' => $transaction->project->code ?? 'no-project',
                'department' => $transaction->department->code ?? 'no-department',
                'date' => $transaction->journalEntry->entry_date->format('Y-m-d'),
                default => 'unknown'
            };
        }

        return implode('|', $parts);
    }
}
