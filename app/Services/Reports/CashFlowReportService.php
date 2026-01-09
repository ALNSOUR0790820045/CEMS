<?php

namespace App\Services\Reports;

class CashFlowReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'cash_flow';
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
        $method = $this->parameters['method'] ?? 'indirect'; // direct or indirect

        $operating = $this->getOperatingActivities($fromDate, $toDate);
        $investing = $this->getInvestingActivities($fromDate, $toDate);
        $financing = $this->getFinancingActivities($fromDate, $toDate);

        $netChange = $operating['total'] + $investing['total'] + $financing['total'];

        return [
            'title' => 'Cash Flow Statement',
            'company' => $this->company->name,
            'period' => sprintf('%s to %s', $this->formatDate($fromDate), $this->formatDate($toDate)),
            'method' => $method,
            'operating_activities' => $operating,
            'investing_activities' => $investing,
            'financing_activities' => $financing,
            'net_change_in_cash' => $netChange,
            'beginning_cash' => 50000.00,
            'ending_cash' => 50000.00 + $netChange,
        ];
    }

    private function getOperatingActivities($fromDate, $toDate): array
    {
        // Placeholder - query GL for operating activities
        return [
            'items' => [
                ['description' => 'Net Income', 'amount' => 20000.00],
                ['description' => 'Depreciation', 'amount' => 5000.00],
                ['description' => 'Accounts Receivable Increase', 'amount' => -3000.00],
                ['description' => 'Accounts Payable Increase', 'amount' => 2000.00],
            ],
            'total' => 24000.00,
        ];
    }

    private function getInvestingActivities($fromDate, $toDate): array
    {
        // Placeholder - query GL for investing activities
        return [
            'items' => [
                ['description' => 'Purchase of Equipment', 'amount' => -15000.00],
                ['description' => 'Sale of Investment', 'amount' => 5000.00],
            ],
            'total' => -10000.00,
        ];
    }

    private function getFinancingActivities($fromDate, $toDate): array
    {
        // Placeholder - query GL for financing activities
        return [
            'items' => [
                ['description' => 'Loan Proceeds', 'amount' => 20000.00],
                ['description' => 'Loan Repayment', 'amount' => -5000.00],
                ['description' => 'Dividends Paid', 'amount' => -3000.00],
            ],
            'total' => 12000.00,
        ];
    }
}
