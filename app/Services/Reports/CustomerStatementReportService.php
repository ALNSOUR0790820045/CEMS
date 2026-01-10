<?php

namespace App\Services\Reports;

class CustomerStatementReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'customer_statement';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['customer_id'])) {
            throw new \InvalidArgumentException('Customer ID is required');
        }
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $customerId = $this->parameters['customer_id'];
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];

        $openingBalance = $this->getOpeningBalance($customerId, $fromDate);
        $transactions = $this->getTransactions($customerId, $fromDate, $toDate);
        $closingBalance = $this->calculateClosingBalance($openingBalance, $transactions);

        return [
            'title' => 'Customer Statement',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'customer' => ['id' => $customerId, 'name' => 'Customer Name'],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'closing_balance' => $closingBalance,
        ];
    }

    private function getOpeningBalance($customerId, $fromDate): float
    {
        // Placeholder - calculate from AR entries
        return 10000.00;
    }

    private function getTransactions($customerId, $fromDate, $toDate): array
    {
        // Placeholder - query AR transactions
        return [
            [
                'date' => '2026-01-01',
                'type' => 'Invoice',
                'reference' => 'INV-001',
                'description' => 'Sale of goods',
                'debit' => 5000.00,
                'credit' => 0.00,
                'balance' => 15000.00,
            ],
            [
                'date' => '2026-01-05',
                'type' => 'Receipt',
                'reference' => 'REC-001',
                'description' => 'Payment received',
                'debit' => 0.00,
                'credit' => 3000.00,
                'balance' => 12000.00,
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
