<?php

namespace App\Services\Reports;

class TrialBalanceReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'trial_balance';
    }

    protected function validateParameters(): void
    {
        // Validate date range
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];
        $accountType = $this->parameters['account_type'] ?? null;
        $costCenter = $this->parameters['cost_center'] ?? null;

        // This is a placeholder structure - in a real implementation,
        // you would query GL accounts and transactions
        $accounts = $this->getTrialBalanceData($fromDate, $toDate, $accountType, $costCenter);

        return [
            'title' => 'Trial Balance',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'headers' => ['Account Code', 'Account Name', 'Opening Balance', 'Debit', 'Credit', 'Closing Balance'],
            'accounts' => $accounts,
            'totals' => $this->calculateTotals($accounts),
        ];
    }

    private function getTrialBalanceData($fromDate, $toDate, $accountType, $costCenter): array
    {
        // Placeholder data structure
        // In real implementation, query from GL tables
        return [
            [
                'code' => '1000',
                'name' => 'Cash',
                'opening_balance' => 10000.00,
                'debit' => 5000.00,
                'credit' => 2000.00,
                'closing_balance' => 13000.00,
            ],
            [
                'code' => '1100',
                'name' => 'Accounts Receivable',
                'opening_balance' => 25000.00,
                'debit' => 8000.00,
                'credit' => 3000.00,
                'closing_balance' => 30000.00,
            ],
            // More accounts would be loaded from database
        ];
    }

    private function calculateTotals(array $accounts): array
    {
        $totals = [
            'opening_balance' => 0,
            'debit' => 0,
            'credit' => 0,
            'closing_balance' => 0,
        ];

        foreach ($accounts as $account) {
            $totals['opening_balance'] += $account['opening_balance'];
            $totals['debit'] += $account['debit'];
            $totals['credit'] += $account['credit'];
            $totals['closing_balance'] += $account['closing_balance'];
        }

        return $totals;
    }
}
