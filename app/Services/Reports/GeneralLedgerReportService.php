<?php

namespace App\Services\Reports;

class GeneralLedgerReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'general_ledger';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['account_id'])) {
            throw new \InvalidArgumentException('Account ID is required');
        }
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $accountId = $this->parameters['account_id'];
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];

        $transactions = $this->getTransactions($accountId, $fromDate, $toDate);
        $openingBalance = $this->getOpeningBalance($accountId, $fromDate);

        return [
            'title' => 'General Ledger Report',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'account' => ['id' => $accountId, 'name' => 'Account Name'],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'closing_balance' => $this->calculateClosingBalance($openingBalance, $transactions),
        ];
    }

    private function getOpeningBalance($accountId, $fromDate): float
    {
        // Placeholder - calculate opening balance from GL entries before from_date
        return 10000.00;
    }

    private function getTransactions($accountId, $fromDate, $toDate): array
    {
        // Placeholder - query GL entries for the account within date range
        return [
            [
                'date' => '2026-01-01',
                'reference' => 'INV-001',
                'description' => 'Customer payment',
                'debit' => 5000.00,
                'credit' => 0.00,
                'balance' => 15000.00,
            ],
            [
                'date' => '2026-01-02',
                'reference' => 'PAY-001',
                'description' => 'Vendor payment',
                'debit' => 0.00,
                'credit' => 2000.00,
                'balance' => 13000.00,
            ],
        ];
    }

    private function calculateClosingBalance($openingBalance, $transactions): float
    {
        $balance = $openingBalance;
        foreach ($transactions as $transaction) {
            $balance += $transaction['debit'] - $transaction['credit'];
        }
        return $balance;
    }
}
