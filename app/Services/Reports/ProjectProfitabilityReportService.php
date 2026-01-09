<?php

namespace App\Services\Reports;

class ProjectProfitabilityReportService extends BaseReportService
{
    public function getReportType(): string
    {
        return 'project_profitability';
    }

    protected function validateParameters(): void
    {
        if (empty($this->parameters['project_id'])) {
            throw new \InvalidArgumentException('Project ID is required');
        }
    }

    public function generate(): array
    {
        $projectId = $this->parameters['project_id'];
        $fromDate = $this->parameters['from_date'] ?? null;
        $toDate = $this->parameters['to_date'] ?? null;

        $revenue = $this->getProjectRevenue($projectId, $fromDate, $toDate);
        $costs = $this->getProjectCosts($projectId, $fromDate, $toDate);
        $budget = $this->getProjectBudget($projectId);

        $grossProfit = $revenue - $costs;
        $marginPercentage = $revenue > 0 ? ($grossProfit / $revenue * 100) : 0;

        return [
            'title' => 'Project Profitability Report',
            'company' => $this->company->name,
            'project' => ['id' => $projectId, 'name' => 'Project Name'],
            'revenue' => $revenue,
            'costs' => $costs,
            'gross_profit' => $grossProfit,
            'margin_percentage' => $marginPercentage,
            'budget' => $budget,
            'budget_variance' => $costs - $budget,
            'budget_variance_percentage' => $budget > 0 ? (($costs - $budget) / $budget * 100) : 0,
        ];
    }

    private function getProjectRevenue($projectId, $fromDate, $toDate): float
    {
        // Placeholder - query revenue for project
        return 100000.00;
    }

    private function getProjectCosts($projectId, $fromDate, $toDate): float
    {
        // Placeholder - query costs for project
        return 65000.00;
    }

    private function getProjectBudget($projectId): float
    {
        // Placeholder - get project budget
        return 70000.00;
    }
}
