<?php

namespace App\Services;

use App\Models\Project;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChartService
{
    /**
     * Get revenue trend chart data (line chart)
     */
    public function getRevenueTrend(): array
    {
        $months = [];
        $revenue = [];
        $expenses = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            
            $months[] = $date->format('M Y');
            
            $monthRevenue = FinancialTransaction::where('type', 'income')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $monthExpense = FinancialTransaction::where('type', 'expense')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $revenue[] = round($monthRevenue, 2);
            $expenses[] = round($monthExpense, 2);
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $revenue,
                    'borderColor' => 'rgb(0, 113, 227)',
                    'backgroundColor' => 'rgba(0, 113, 227, 0.1)',
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenses,
                    'borderColor' => 'rgb(255, 59, 48)',
                    'backgroundColor' => 'rgba(255, 59, 48, 0.1)',
                ],
            ],
        ];
    }

    /**
     * Get project status distribution (pie chart)
     */
    public function getProjectStatusDistribution(): array
    {
        $statuses = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'active' => 'rgb(52, 199, 89)',
            'completed' => 'rgb(0, 113, 227)',
            'on_hold' => 'rgb(255, 204, 0)',
            'delayed' => 'rgb(255, 59, 48)',
        ];
        $backgroundColors = [];

        foreach ($statuses as $status) {
            $labels[] = ucfirst($status->status);
            $data[] = $status->count;
            $backgroundColors[] = $colors[$status->status] ?? 'rgb(142, 142, 147)';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }

    /**
     * Get project budget comparison (bar chart)
     */
    public function getProjectBudgetComparison(): array
    {
        $projects = Project::select('name', 'budget', 'actual_cost')
            ->where('status', '!=', 'completed')
            ->limit(10)
            ->get();

        $labels = [];
        $budgetData = [];
        $actualData = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;
            $budgetData[] = round($project->budget, 2);
            $actualData[] = round($project->actual_cost, 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Budget',
                    'data' => $budgetData,
                    'backgroundColor' => 'rgba(0, 113, 227, 0.8)',
                ],
                [
                    'label' => 'Actual Cost',
                    'data' => $actualData,
                    'backgroundColor' => 'rgba(255, 149, 0, 0.8)',
                ],
            ],
        ];
    }

    /**
     * Get expense breakdown by category (pie chart)
     */
    public function getExpenseBreakdown(): array
    {
        $expenses = FinancialTransaction::where('type', 'expense')
            ->where('status', 'completed')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'rgb(0, 113, 227)',
            'rgb(255, 59, 48)',
            'rgb(52, 199, 89)',
            'rgb(255, 149, 0)',
            'rgb(175, 82, 222)',
        ];

        foreach ($expenses as $index => $expense) {
            $labels[] = ucfirst($expense->category);
            $data[] = round($expense->total, 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
        ];
    }

    /**
     * Get revenue by project (bar chart)
     */
    public function getRevenueByProject(): array
    {
        $projectRevenues = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->whereNotNull('project_id')
            ->select('project_id', DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('project_id')
            ->with('project:id,name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($projectRevenues as $revenue) {
            if ($revenue->project) {
                $labels[] = $revenue->project->name;
                $data[] = round($revenue->total_revenue, 2);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'backgroundColor' => 'rgba(52, 199, 89, 0.8)',
                ],
            ],
        ];
    }

    /**
     * Get cash flow trend (line chart)
     */
    public function getCashFlowTrend(): array
    {
        $months = [];
        $cashFlow = [];
        $cumulative = 0;

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $months[] = $date->format('M Y');
            
            $monthRevenue = FinancialTransaction::where('type', 'income')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $monthExpense = FinancialTransaction::where('type', 'expense')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            
            $cumulative += ($monthRevenue - $monthExpense);
            $cashFlow[] = round($cumulative, 2);
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Cash Flow',
                    'data' => $cashFlow,
                    'borderColor' => 'rgb(52, 199, 89)',
                    'backgroundColor' => 'rgba(52, 199, 89, 0.1)',
                    'fill' => true,
                ],
            ],
        ];
    }

    /**
     * Get chart by type
     */
    public function getChartByType(string $type): array
    {
        return match($type) {
            'revenue-trend' => $this->getRevenueTrend(),
            'project-status' => $this->getProjectStatusDistribution(),
            'project-budget' => $this->getProjectBudgetComparison(),
            'expense-breakdown' => $this->getExpenseBreakdown(),
            'revenue-by-project' => $this->getRevenueByProject(),
            'cash-flow' => $this->getCashFlowTrend(),
            default => ['error' => 'Invalid chart type'],
        };
    }
}
