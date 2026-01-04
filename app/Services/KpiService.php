<?php

namespace App\Services;

use App\Models\Project;
use App\Models\FinancialTransaction;
use App\Models\Inventory;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiService
{
    /**
     * Get all KPIs for executive dashboard
     */
    public function getAllKpis(): array
    {
        return [
            'financial' => $this->getFinancialKpis(),
            'project' => $this->getProjectKpis(),
            'operational' => $this->getOperationalKpis(),
            'hr' => $this->getHrKpis(),
        ];
    }

    /**
     * Get financial KPIs
     */
    public function getFinancialKpis(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Revenue
        $monthlyRevenue = FinancialTransaction::where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        $yearlyRevenue = FinancialTransaction::where('type', 'income')
            ->whereYear('date', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        // Expenses
        $monthlyExpenses = FinancialTransaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        $yearlyExpenses = FinancialTransaction::where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        // Profit
        $monthlyProfit = $monthlyRevenue - $monthlyExpenses;
        $yearlyProfit = $yearlyRevenue - $yearlyExpenses;

        // Profit Margin
        $profitMargin = $yearlyRevenue > 0 ? ($yearlyProfit / $yearlyRevenue) * 100 : 0;

        // Cash (simplified - would normally be from cash accounts)
        $cashBalance = $yearlyRevenue - $yearlyExpenses;

        // AR/AP (Outstanding receivables/payables)
        $accountsReceivable = FinancialTransaction::where('type', 'income')
            ->where('status', 'pending')
            ->sum('amount');

        $accountsPayable = FinancialTransaction::where('type', 'expense')
            ->where('status', 'pending')
            ->sum('amount');

        return [
            'monthly_revenue' => round($monthlyRevenue, 2),
            'yearly_revenue' => round($yearlyRevenue, 2),
            'monthly_expenses' => round($monthlyExpenses, 2),
            'yearly_expenses' => round($yearlyExpenses, 2),
            'monthly_profit' => round($monthlyProfit, 2),
            'yearly_profit' => round($yearlyProfit, 2),
            'profit_margin' => round($profitMargin, 2),
            'cash_balance' => round($cashBalance, 2),
            'accounts_receivable' => round($accountsReceivable, 2),
            'accounts_payable' => round($accountsPayable, 2),
        ];
    }

    /**
     * Get project KPIs
     */
    public function getProjectKpis(): array
    {
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalProjects = Project::count();

        $avgProgress = Project::where('status', 'active')->avg('progress') ?? 0;
        
        $totalBudget = Project::sum('budget');
        $totalActualCost = Project::sum('actual_cost');
        
        $budgetVariance = $totalBudget - $totalActualCost;
        $budgetUtilization = $totalBudget > 0 ? ($totalActualCost / $totalBudget) * 100 : 0;

        // Overall SPI and CPI
        $totalPV = Project::sum('planned_value');
        $totalEV = Project::sum('earned_value');
        $totalAC = Project::sum('actual_cost');

        $overallSpi = $totalPV > 0 ? $totalEV / $totalPV : 0;
        $overallCpi = $totalAC > 0 ? $totalEV / $totalAC : 0;

        return [
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects,
            'total_projects' => $totalProjects,
            'average_progress' => round($avgProgress, 2),
            'total_budget' => round($totalBudget, 2),
            'total_actual_cost' => round($totalActualCost, 2),
            'budget_variance' => round($budgetVariance, 2),
            'budget_utilization' => round($budgetUtilization, 2),
            'overall_spi' => round($overallSpi, 2),
            'overall_cpi' => round($overallCpi, 2),
        ];
    }

    /**
     * Get operational KPIs
     */
    public function getOperationalKpis(): array
    {
        $inventoryValue = Inventory::sum('total_value');
        $inventoryItems = Inventory::count();

        $pendingProcurement = FinancialTransaction::where('type', 'expense')
            ->where('category', 'materials')
            ->where('status', 'pending')
            ->count();

        return [
            'inventory_value' => round($inventoryValue, 2),
            'inventory_items' => $inventoryItems,
            'pending_procurement' => $pendingProcurement,
        ];
    }

    /**
     * Get HR KPIs
     */
    public function getHrKpis(): array
    {
        $employeeCount = User::count();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $attendanceRate = 0;
        $totalAttendance = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();
        
        $presentCount = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'present')
            ->count();

        if ($totalAttendance > 0) {
            $attendanceRate = ($presentCount / $totalAttendance) * 100;
        }

        // Payroll (simplified - sum of estimated salaries based on worked hours)
        $monthlyPayroll = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('hours_worked') * 50; // Assuming 50 per hour average

        return [
            'employee_count' => $employeeCount,
            'attendance_rate' => round($attendanceRate, 2),
            'monthly_payroll' => round($monthlyPayroll, 2),
        ];
    }

    /**
     * Get project-specific KPIs
     */
    public function getProjectSpecificKpis(int $projectId): array
    {
        $project = Project::findOrFail($projectId);

        $projectRevenue = FinancialTransaction::where('project_id', $projectId)
            ->where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $projectExpenses = FinancialTransaction::where('project_id', $projectId)
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $projectProfit = $projectRevenue - $projectExpenses;

        return [
            'project' => $project,
            'spi' => $project->spi,
            'cpi' => $project->cpi,
            'progress' => $project->progress,
            'budget' => $project->budget,
            'actual_cost' => $project->actual_cost,
            'earned_value' => $project->earned_value,
            'planned_value' => $project->planned_value,
            'budget_remaining' => $project->budget - $project->actual_cost,
            'project_revenue' => round($projectRevenue, 2),
            'project_expenses' => round($projectExpenses, 2),
            'project_profit' => round($projectProfit, 2),
        ];
    }
}
