<?php

namespace App\Services\Reports;

class IncomeStatementReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'income_statement';
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

        $revenue = $this->getRevenue($fromDate, $toDate);
        $expenses = $this->getExpenses($fromDate, $toDate);
        
        $totalRevenue = array_sum(array_column($revenue, 'amount'));
        $totalExpenses = array_sum(array_column($expenses, 'amount'));
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'title' => 'Income Statement (P&L)',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'revenue' => $revenue,
            'expenses' => $expenses,
            'totals' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'net_income_percentage' => $totalRevenue > 0 ? ($netIncome / $totalRevenue * 100) : 0,
            ],
        ];
    }

    private function getRevenue($fromDate, $toDate): array
    {
        // Placeholder - query from GL accounts where account_type = 'revenue'
        return [
            ['name' => 'Sales Revenue', 'amount' => 150000.00],
            ['name' => 'Service Revenue', 'amount' => 50000.00],
            ['name' => 'Other Income', 'amount' => 5000.00],
        ];
    }

    private function getExpenses($fromDate, $toDate): array
    {
        // Placeholder - query from GL accounts where account_type = 'expense'
        return [
            ['name' => 'Cost of Goods Sold', 'amount' => 80000.00],
            ['name' => 'Salaries Expense', 'amount' => 40000.00],
            ['name' => 'Rent Expense', 'amount' => 12000.00],
            ['name' => 'Utilities Expense', 'amount' => 3000.00],
            ['name' => 'Marketing Expense', 'amount' => 8000.00],
        ];
    }
}
