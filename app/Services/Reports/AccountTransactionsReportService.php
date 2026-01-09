<?php

namespace App\Services\Reports;

class AccountTransactionsReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'account_transactions';
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
        $transactionType = $this->parameters['transaction_type'] ?? null;

        $openingBalance = $this->getOpeningBalance($accountId, $fromDate);
        $transactions = $this->getAccountTransactions($accountId, $fromDate, $toDate, $transactionType);

        return [
            'title' => 'Account Transactions Report',
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
        // Placeholder - calculate opening balance
        return 10000.00;
    }

    private function getAccountTransactions($accountId, $fromDate, $toDate, $transactionType): array
    {
        // Placeholder - query account transactions with running balance
        return [
            [
                'date' => '2026-01-01',
                'reference' => 'JE-001',
                'description' => 'Opening entry',
                'transaction_type' => 'Journal Entry',
                'debit' => 5000.00,
                'credit' => 0.00,
                'balance' => 15000.00,
            ],
            [
                'date' => '2026-01-02',
                'reference' => 'INV-001',
                'description' => 'Customer invoice',
                'transaction_type' => 'Invoice',
                'debit' => 3000.00,
                'credit' => 0.00,
                'balance' => 18000.00,
            ],
            [
                'date' => '2026-01-03',
                'reference' => 'PAY-001',
                'description' => 'Vendor payment',
                'transaction_type' => 'Payment',
                'debit' => 0.00,
                'credit' => 2000.00,
                'balance' => 16000.00,
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
