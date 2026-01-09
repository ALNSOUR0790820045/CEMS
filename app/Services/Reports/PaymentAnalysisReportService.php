<?php

namespace App\Services\Reports;

class PaymentAnalysisReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'payment_analysis';
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
        $paymentMethod = $this->parameters['payment_method'] ?? null;
        $partyType = $this->parameters['party_type'] ?? null; // vendor or customer

        $byMethod = $this->getPaymentsByMethod($fromDate, $toDate, $partyType);
        $byParty = $this->getPaymentsByParty($fromDate, $toDate, $paymentMethod, $partyType);

        return [
            'title' => 'Payment Analysis Report',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'by_payment_method' => $byMethod,
            'by_party' => $byParty,
            'summary' => [
                'total_paid' => array_sum(array_column($byMethod, 'amount')),
                'total_pending' => 15000.00, // Placeholder
            ],
        ];
    }

    private function getPaymentsByMethod($fromDate, $toDate, $partyType): array
    {
        // Placeholder - query payments grouped by method
        return [
            ['payment_method' => 'Cash', 'amount' => 25000.00, 'count' => 15],
            ['payment_method' => 'Bank Transfer', 'amount' => 45000.00, 'count' => 20],
            ['payment_method' => 'Check', 'amount' => 18000.00, 'count' => 8],
            ['payment_method' => 'Credit Card', 'amount' => 12000.00, 'count' => 10],
        ];
    }

    private function getPaymentsByParty($fromDate, $toDate, $paymentMethod, $partyType): array
    {
        // Placeholder - query payments grouped by vendor/customer
        return [
            ['party_name' => 'ABC Supplier', 'amount' => 30000.00, 'count' => 12],
            ['party_name' => 'XYZ Corp', 'amount' => 25000.00, 'count' => 10],
            ['party_name' => 'Customer A', 'amount' => 45000.00, 'count' => 21],
        ];
    }
}
