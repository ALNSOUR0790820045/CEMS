<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KpiService;
use App\Models\Company;
use App\Models\Project;
use App\Models\FinancialTransaction;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KpiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $kpiService;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kpiService = new KpiService();
        
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'SA',
            'is_active' => true,
        ]);
    }

    public function test_financial_kpis_calculates_revenue_correctly()
    {
        // Create income transactions
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
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 50000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $kpis = $this->kpiService->getFinancialKpis();

        $this->assertEquals(150000, $kpis['monthly_revenue']);
        $this->assertEquals(150000, $kpis['yearly_revenue']);
    }

    public function test_financial_kpis_calculates_profit_margin()
    {
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

        $kpis = $this->kpiService->getFinancialKpis();

        // Profit = 100000 - 60000 = 40000
        // Margin = (40000 / 100000) * 100 = 40%
        $this->assertEquals(40000, $kpis['yearly_profit']);
        $this->assertEquals(40, $kpis['profit_margin']);
    }

    public function test_project_kpis_counts_projects_by_status()
    {
        Project::create([
            'name' => 'Active Project 1',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 100000,
        ]);

        Project::create([
            'name' => 'Active Project 2',
            'code' => 'PRJ-002',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 200000,
        ]);

        Project::create([
            'name' => 'Completed Project',
            'code' => 'PRJ-003',
            'company_id' => $this->company->id,
            'status' => 'completed',
            'start_date' => now()->subMonths(6),
            'budget' => 150000,
        ]);

        $kpis = $this->kpiService->getProjectKpis();

        $this->assertEquals(2, $kpis['active_projects']);
        $this->assertEquals(1, $kpis['completed_projects']);
        $this->assertEquals(3, $kpis['total_projects']);
    }

    public function test_project_kpis_calculates_average_progress()
    {
        Project::create([
            'name' => 'Project 1',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 100000,
            'progress' => 50,
        ]);

        Project::create([
            'name' => 'Project 2',
            'code' => 'PRJ-002',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 200000,
            'progress' => 70,
        ]);

        $kpis = $this->kpiService->getProjectKpis();

        // Average = (50 + 70) / 2 = 60
        $this->assertEquals(60, $kpis['average_progress']);
    }

    public function test_project_kpis_calculates_budget_utilization()
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

        $kpis = $this->kpiService->getProjectKpis();

        // Utilization = (80000 / 100000) * 100 = 80%
        $this->assertEquals(80, $kpis['budget_utilization']);
    }

    public function test_operational_kpis_calculates_inventory_value()
    {
        Inventory::create([
            'company_id' => $this->company->id,
            'item_name' => 'Cement',
            'category' => 'Materials',
            'quantity' => 100,
            'unit' => 'bags',
            'unit_price' => 25,
            'total_value' => 2500,
        ]);

        Inventory::create([
            'company_id' => $this->company->id,
            'item_name' => 'Steel',
            'category' => 'Materials',
            'quantity' => 50,
            'unit' => 'tons',
            'unit_price' => 1000,
            'total_value' => 50000,
        ]);

        $kpis = $this->kpiService->getOperationalKpis();

        $this->assertEquals(52500, $kpis['inventory_value']);
        $this->assertEquals(2, $kpis['inventory_items']);
    }

    public function test_hr_kpis_counts_employees()
    {
        User::create([
            'name' => 'Employee 1',
            'email' => 'emp1@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        User::create([
            'name' => 'Employee 2',
            'email' => 'emp2@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $kpis = $this->kpiService->getHrKpis();

        $this->assertEquals(2, $kpis['employee_count']);
    }

    public function test_hr_kpis_calculates_attendance_rate()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        // Create 10 days of attendance, 8 present, 2 absent
        for ($i = 0; $i < 8; $i++) {
            Attendance::create([
                'company_id' => $this->company->id,
                'user_id' => $user->id,
                'date' => now()->subDays($i),
                'status' => 'present',
                'hours_worked' => 8,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            Attendance::create([
                'company_id' => $this->company->id,
                'user_id' => $user->id,
                'date' => now()->subDays($i + 8),
                'status' => 'absent',
                'hours_worked' => 0,
            ]);
        }

        $kpis = $this->kpiService->getHrKpis();

        // Attendance rate = (8 / 10) * 100 = 80%
        $this->assertEquals(80, $kpis['attendance_rate']);
    }

    public function test_get_all_kpis_returns_all_categories()
    {
        $kpis = $this->kpiService->getAllKpis();

        $this->assertArrayHasKey('financial', $kpis);
        $this->assertArrayHasKey('project', $kpis);
        $this->assertArrayHasKey('operational', $kpis);
        $this->assertArrayHasKey('hr', $kpis);
    }

    public function test_project_specific_kpis_returns_project_details()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 1000000,
            'actual_cost' => 600000,
            'planned_value' => 500000,
            'earned_value' => 550000,
            'progress' => 55,
        ]);

        $kpis = $this->kpiService->getProjectSpecificKpis($project->id);

        $this->assertEquals('Test Project', $kpis['project']->name);
        $this->assertEquals(1000000, $kpis['budget']);
        $this->assertEquals(600000, $kpis['actual_cost']);
        $this->assertEquals(400000, $kpis['budget_remaining']);
    }
}
