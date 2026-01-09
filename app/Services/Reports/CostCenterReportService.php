<?php

namespace App\Services\Reports;

class CostCenterReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'cost_center';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['cost_center_id'])) {
            throw new \InvalidArgumentException('Cost Center ID is required');
        }
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $costCenterId = $this->parameters['cost_center_id'];
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];

        $income = $this->getCostCenterIncome($costCenterId, $fromDate, $toDate);
        $expenses = $this->getCostCenterExpenses($costCenterId, $fromDate, $toDate);
        $budget = $this->getCostCenterBudget($costCenterId, $fromDate, $toDate);

        $totalIncome = array_sum(array_column($income, 'amount'));
        $totalExpenses = array_sum(array_column($expenses, 'amount'));

        return [
            'title' => 'Cost Center Report',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'cost_center' => ['id' => $costCenterId, 'name' => 'Cost Center Name'],
            'income' => $income,
            'expenses' => $expenses,
            'totals' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net' => $totalIncome - $totalExpenses,
            ],
            'budget' => $budget,
        ];
    }

    private function getCostCenterIncome($costCenterId, $fromDate, $toDate): array
    {
        // Placeholder - query income for cost center
        return [
            ['account' => 'Revenue Account 1', 'amount' => 50000.00],
            ['account' => 'Revenue Account 2', 'amount' => 30000.00],
        ];
    }

    private function getCostCenterExpenses($costCenterId, $fromDate, $toDate): array
    {
        // Placeholder - query expenses for cost center
        return [
            ['account' => 'Salary Expense', 'amount' => 40000.00],
            ['account' => 'Operating Expense', 'amount' => 15000.00],
        ];
    }

    private function getCostCenterBudget($costCenterId, $fromDate, $toDate): array
    {
        // Placeholder - get budget data
        return [
            'budgeted_income' => 85000.00,
            'budgeted_expenses' => 60000.00,
            'income_variance' => -5000.00,
            'expense_variance' => 5000.00,
        ];
    }
}
