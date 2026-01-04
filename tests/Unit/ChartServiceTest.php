<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ChartService;
use App\Models\Company;
use App\Models\Project;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $chartService;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->chartService = new ChartService();
        
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'SA',
            'is_active' => true,
        ]);
    }

    public function test_revenue_trend_returns_chart_data()
    {
        // Create some transactions
        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 50000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $chartData = $this->chartService->getRevenueTrend();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertCount(12, $chartData['labels']); // 12 months
        $this->assertCount(2, $chartData['datasets']); // Revenue and Expenses
    }

    public function test_project_status_distribution_returns_chart_data()
    {
        Project::create([
            'name' => 'Active Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 100000,
        ]);

        Project::create([
            'name' => 'Completed Project',
            'code' => 'PRJ-002',
            'company_id' => $this->company->id,
            'status' => 'completed',
            'start_date' => now()->subMonths(6),
            'budget' => 200000,
        ]);

        $chartData = $this->chartService->getProjectStatusDistribution();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertGreaterThan(0, count($chartData['labels']));
    }

    public function test_project_budget_comparison_returns_chart_data()
    {
        Project::create([
            'name' => 'Project 1',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 100000,
            'actual_cost' => 80000,
        ]);

        $chartData = $this->chartService->getProjectBudgetComparison();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertCount(2, $chartData['datasets']); // Budget and Actual
    }

    public function test_expense_breakdown_returns_chart_data()
    {
        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'expense',
            'category' => 'materials',
            'amount' => 30000,
            'date' => now(),
            'status' => 'completed',
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'expense',
            'category' => 'labor',
            'amount' => 20000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $chartData = $this->chartService->getExpenseBreakdown();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertGreaterThan(0, count($chartData['labels']));
    }

    public function test_revenue_by_project_returns_chart_data()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 100000,
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 50000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $chartData = $this->chartService->getRevenueByProject();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
    }

    public function test_cash_flow_trend_returns_chart_data()
    {
        // Create income and expense
        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 100000,
            'date' => now(),
            'status' => 'completed',
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'expense',
            'category' => 'materials',
            'amount' => 60000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $chartData = $this->chartService->getCashFlowTrend();

        $this->assertIsArray($chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertCount(12, $chartData['labels']); // 12 months
    }

    public function test_get_chart_by_type_returns_correct_chart()
    {
        $revenueTrend = $this->chartService->getChartByType('revenue-trend');
        $this->assertArrayHasKey('labels', $revenueTrend);

        $projectStatus = $this->chartService->getChartByType('project-status');
        $this->assertArrayHasKey('labels', $projectStatus);

        $projectBudget = $this->chartService->getChartByType('project-budget');
        $this->assertArrayHasKey('labels', $projectBudget);

        $expenseBreakdown = $this->chartService->getChartByType('expense-breakdown');
        $this->assertArrayHasKey('labels', $expenseBreakdown);

        $revenueByProject = $this->chartService->getChartByType('revenue-by-project');
        $this->assertArrayHasKey('labels', $revenueByProject);

        $cashFlow = $this->chartService->getChartByType('cash-flow');
        $this->assertArrayHasKey('labels', $cashFlow);
    }

    public function test_get_chart_by_type_returns_error_for_invalid_type()
    {
        $result = $this->chartService->getChartByType('invalid-type');
        
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Invalid chart type', $result['error']);
    }

    public function test_revenue_trend_includes_correct_number_of_months()
    {
        $chartData = $this->chartService->getRevenueTrend();

        // Should include data for last 12 months
        $this->assertCount(12, $chartData['labels']);
        $this->assertCount(2, $chartData['datasets']);
        
        // Each dataset should have 12 data points
        $this->assertCount(12, $chartData['datasets'][0]['data']);
        $this->assertCount(12, $chartData['datasets'][1]['data']);
    }

    public function test_cash_flow_trend_is_cumulative()
    {
        // Create transactions over multiple months
        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 100000,
            'date' => Carbon::now()->subMonth(),
            'status' => 'completed',
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'expense',
            'category' => 'materials',
            'amount' => 40000,
            'date' => Carbon::now()->subMonth(),
            'status' => 'completed',
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 50000,
            'date' => Carbon::now(),
            'status' => 'completed',
        ]);

        $chartData = $this->chartService->getCashFlowTrend();

        // Cash flow should be cumulative
        $this->assertIsArray($chartData['datasets'][0]['data']);
        $this->assertCount(12, $chartData['datasets'][0]['data']);
    }
}
