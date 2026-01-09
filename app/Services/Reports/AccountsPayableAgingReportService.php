<?php

namespace App\Services\Reports;

class AccountsPayableAgingReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'ap_aging';
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
        
        $vendors = $this->getVendorAging($asOfDate);

        return [
            'title' => 'Accounts Payable Aging Report',
            'company' => $this->company->name,
            'as_of_date' => $this->formatDate($asOfDate),
            'vendors' => $vendors,
            'totals' => $this->calculateAgingTotals($vendors),
        ];
    }

    private function getVendorAging($asOfDate): array
    {
        // Placeholder - query AP invoices and calculate aging buckets
        return [
            [
                'vendor_name' => 'ABC Supplier',
                'current' => 5000.00,
                '1_30_days' => 2000.00,
                '31_60_days' => 1000.00,
                '61_90_days' => 500.00,
                'over_90_days' => 200.00,
                'total' => 8700.00,
            ],
            [
                'vendor_name' => 'XYZ Corp',
                'current' => 3000.00,
                '1_30_days' => 1500.00,
                '31_60_days' => 0.00,
                '61_90_days' => 0.00,
                'over_90_days' => 0.00,
                'total' => 4500.00,
            ],
        ];
    }

    private function calculateAgingTotals($vendors): array
    {
        $totals = [
            'current' => 0,
            '1_30_days' => 0,
            '31_60_days' => 0,
            '61_90_days' => 0,
            'over_90_days' => 0,
            'total' => 0,
        ];

        foreach ($vendors as $vendor) {
            $totals['current'] += $vendor['current'];
            $totals['1_30_days'] += $vendor['1_30_days'];
            $totals['31_60_days'] += $vendor['31_60_days'];
            $totals['61_90_days'] += $vendor['61_90_days'];
            $totals['over_90_days'] += $vendor['over_90_days'];
            $totals['total'] += $vendor['total'];
        }

        return $totals;
    }
}
