<?php

namespace App\Services\Reports;

class AccountsReceivableAgingReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'ar_aging';
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
        
        $customers = $this->getCustomerAging($asOfDate);

        return [
            'title' => 'Accounts Receivable Aging Report',
            'company' => $this->company->name,
            'as_of_date' => $this->formatDate($asOfDate),
            'customers' => $customers,
            'totals' => $this->calculateAgingTotals($customers),
        ];
    }

    private function getCustomerAging($asOfDate): array
    {
        // Placeholder - query AR invoices and calculate aging buckets
        return [
            [
                'customer_name' => 'Customer A',
                'current' => 10000.00,
                '1_30_days' => 3000.00,
                '31_60_days' => 1500.00,
                '61_90_days' => 500.00,
                'over_90_days' => 0.00,
                'total' => 15000.00,
            ],
            [
                'customer_name' => 'Customer B',
                'current' => 8000.00,
                '1_30_days' => 2000.00,
                '31_60_days' => 0.00,
                '61_90_days' => 0.00,
                'over_90_days' => 0.00,
                'total' => 10000.00,
            ],
        ];
    }

    private function calculateAgingTotals($customers): array
    {
        $totals = [
            'current' => 0,
            '1_30_days' => 0,
            '31_60_days' => 0,
            '61_90_days' => 0,
            'over_90_days' => 0,
            'total' => 0,
        ];

        foreach ($customers as $customer) {
            $totals['current'] += $customer['current'];
            $totals['1_30_days'] += $customer['1_30_days'];
            $totals['31_60_days'] += $customer['31_60_days'];
            $totals['61_90_days'] += $customer['61_90_days'];
            $totals['over_90_days'] += $customer['over_90_days'];
            $totals['total'] += $customer['total'];
        }

        return $totals;
    }
}
