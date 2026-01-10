<?php

namespace App\Services\Reports;

class TaxReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'tax_report';
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
        $taxType = $this->parameters['tax_type'] ?? 'VAT';

        $salesTax = $this->getSalesTaxCollected($fromDate, $toDate);
        $purchaseTax = $this->getPurchaseTaxPaid($fromDate, $toDate);
        
        $netTaxPayable = $salesTax['total'] - $purchaseTax['total'];

        return [
            'title' => 'Tax Report (VAT)',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'tax_type' => $taxType,
            'sales_tax' => $salesTax,
            'purchase_tax' => $purchaseTax,
            'net_tax_payable' => $netTaxPayable,
            'status' => $netTaxPayable >= 0 ? 'Payable' : 'Receivable',
        ];
    }

    private function getSalesTaxCollected($fromDate, $toDate): array
    {
        // Placeholder - query sales tax from invoices
        return [
            'items' => [
                ['tax_rate' => '5%', 'taxable_amount' => 100000.00, 'tax_amount' => 5000.00],
                ['tax_rate' => '10%', 'taxable_amount' => 50000.00, 'tax_amount' => 5000.00],
            ],
            'total' => 10000.00,
        ];
    }

    private function getPurchaseTaxPaid($fromDate, $toDate): array
    {
        // Placeholder - query purchase tax from bills
        return [
            'items' => [
                ['tax_rate' => '5%', 'taxable_amount' => 60000.00, 'tax_amount' => 3000.00],
                ['tax_rate' => '10%', 'taxable_amount' => 30000.00, 'tax_amount' => 3000.00],
            ],
            'total' => 6000.00,
        ];
    }
}
