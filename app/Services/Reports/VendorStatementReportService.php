<?php

namespace App\Services\Reports;

class VendorStatementReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'vendor_statement';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['vendor_id'])) {
            throw new \InvalidArgumentException('Vendor ID is required');
        }
        if (empty($this->parameters['from_date']) || empty($this->parameters['to_date'])) {
            throw new \InvalidArgumentException('Date range is required');
        }
    }

    public function generate(): array
    {
        $vendorId = $this->parameters['vendor_id'];
        $fromDate = $this->parameters['from_date'];
        $toDate = $this->parameters['to_date'];

        $openingBalance = $this->getOpeningBalance($vendorId, $fromDate);
        $transactions = $this->getTransactions($vendorId, $fromDate, $toDate);
        $closingBalance = $this->calculateClosingBalance($openingBalance, $transactions);

        return [
            'title' => 'Vendor Statement',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'vendor' => ['id' => $vendorId, 'name' => 'Vendor Name'],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'closing_balance' => $closingBalance,
        ];
    }

    private function getOpeningBalance($vendorId, $fromDate): float
    {
        // Placeholder - calculate from AP entries
        return 5000.00;
    }

    private function getTransactions($vendorId, $fromDate, $toDate): array
    {
        // Placeholder - query AP transactions
        return [
            [
                'date' => '2026-01-01',
                'type' => 'Invoice',
                'reference' => 'INV-001',
                'description' => 'Purchase of goods',
                'debit' => 3000.00,
                'credit' => 0.00,
                'balance' => 8000.00,
            ],
            [
                'date' => '2026-01-05',
                'type' => 'Payment',
                'reference' => 'PAY-001',
                'description' => 'Payment made',
                'debit' => 0.00,
                'credit' => 2000.00,
                'balance' => 6000.00,
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
