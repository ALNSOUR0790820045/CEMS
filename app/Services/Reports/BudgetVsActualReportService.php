<?php

namespace App\Services\Reports;

class BudgetVsActualReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'budget_vs_actual';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];
        $breakdown = $this->parameters['breakdown'] ?? 'total'; // monthly, quarterly, yearly
        $accountId = $this->parameters['account_id'] ?? null;
        $costCenterId = $this->parameters['cost_center_id'] ?? null;

        $data = $this->getBudgetVsActualData($fromDate, $toDate, $breakdown, $accountId, $costCenterId);

        return [
            'title' => 'Budget vs Actual Report',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'breakdown' => $breakdown,
            'data' => $data,
            'summary' => $this->calculateSummary($data),
        ];
    }

    private function getBudgetVsActualData($fromDate, $toDate, $breakdown, $accountId, $costCenterId): array
    {
        // Placeholder - query budget and actual data
        return [
            [
                'account' => 'Revenue',
                'budgeted' => 200000.00,
                'actual' => 205000.00,
                'variance' => 5000.00,
                'variance_percentage' => 2.5,
            ],
            [
                'account' => 'Salary Expense',
                'budgeted' => 80000.00,
                'actual' => 82000.00,
                'variance' => -2000.00,
                'variance_percentage' => -2.5,
            ],
            [
                'account' => 'Operating Expense',
                'budgeted' => 30000.00,
                'actual' => 28000.00,
                'variance' => 2000.00,
                'variance_percentage' => 6.67,
            ],
        ];
    }

    private function calculateSummary($data): array
    {
        $totalBudgeted = array_sum(array_column($data, 'budgeted'));
        $totalActual = array_sum(array_column($data, 'actual'));
        $totalVariance = $totalActual - $totalBudgeted;

        return [
            'total_budgeted' => $totalBudgeted,
            'total_actual' => $totalActual,
            'total_variance' => $totalVariance,
            'total_variance_percentage' => $totalBudgeted > 0 ? ($totalVariance / $totalBudgeted * 100) : 0,
        ];
    }
}
