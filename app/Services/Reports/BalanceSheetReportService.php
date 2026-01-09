<?php

namespace App\Services\Reports;

class BalanceSheetReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'balance_sheet';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['as_of_date'])) {
            throw new \InvalidArgumentException('As of date is required');
        }
    }

    public function generate(): array
    {
        $asOfDate = $this->parameters['as_of_date'];
        $comparative = $this->parameters['comparative'] ?? false;

        $assets = $this->getAssets($asOfDate);
        $liabilities = $this->getLiabilities($asOfDate);
        $equity = $this->getEquity($asOfDate);

        return [
            'title' => 'Balance Sheet',
            'company' => $this->company->name,
            'as_of_date' => $this->formatDate($asOfDate),
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totals' => [
                'total_assets' => array_sum(array_column($assets, 'amount')),
                'total_liabilities' => array_sum(array_column($liabilities, 'amount')),
                'total_equity' => array_sum(array_column($equity, 'amount')),
            ],
        ];
    }

    private function getAssets($asOfDate): array
    {
        // Placeholder - query from GL accounts where account_type = 'asset'
        return [
            ['category' => 'Current Assets', 'name' => 'Cash', 'amount' => 50000.00],
            ['category' => 'Current Assets', 'name' => 'Accounts Receivable', 'amount' => 30000.00],
            ['category' => 'Fixed Assets', 'name' => 'Equipment', 'amount' => 100000.00],
        ];
    }

    private function getLiabilities($asOfDate): array
    {
        // Placeholder - query from GL accounts where account_type = 'liability'
        return [
            ['category' => 'Current Liabilities', 'name' => 'Accounts Payable', 'amount' => 20000.00],
            ['category' => 'Long-term Liabilities', 'name' => 'Loan Payable', 'amount' => 50000.00],
        ];
    }

    private function getEquity($asOfDate): array
    {
        // Placeholder - query from GL accounts where account_type = 'equity'
        return [
            ['category' => 'Equity', 'name' => 'Owner\'s Equity', 'amount' => 80000.00],
            ['category' => 'Equity', 'name' => 'Retained Earnings', 'amount' => 30000.00],
        ];
    }
}
