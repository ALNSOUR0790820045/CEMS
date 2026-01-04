<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\FinancialTransaction;
use App\Models\Inventory;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'name_en' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'SA',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);
    }

    public function test_executive_dashboard_returns_kpis()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/dashboard/executive');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'kpis' => [
                        'financial',
                        'project',
                        'operational',
                        'hr',
                    ],
                    'timestamp',
                ],
            ]);
    }

    public function test_executive_dashboard_requires_authentication()
    {
        $response = $this->getJson('/api/dashboard/executive');

        $response->assertStatus(401);
    }

    public function test_project_dashboard_returns_project_kpis()
    {
        Sanctum::actingAs($this->user);

        // Create a test project
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 1000000,
            'planned_value' => 500000,
            'earned_value' => 400000,
            'actual_cost' => 450000,
            'progress' => 50,
        ]);

        $response = $this->getJson("/api/dashboard/project/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'project',
                    'spi',
                    'cpi',
                    'progress',
                    'budget',
                    'actual_cost',
                ],
            ]);
    }

    public function test_project_dashboard_returns_404_for_invalid_project()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/dashboard/project/9999');

        $response->assertStatus(404);
    }

    public function test_financial_dashboard_returns_financial_kpis()
    {
        Sanctum::actingAs($this->user);

        // Create some financial transactions
        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'income',
            'category' => 'revenue',
            'amount' => 50000,
            'date' => now(),
            'status' => 'completed',
        ]);

        FinancialTransaction::create([
            'company_id' => $this->company->id,
            'type' => 'expense',
            'category' => 'materials',
            'amount' => 20000,
            'date' => now(),
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/dashboard/financial');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'financial_kpis' => [
                        'monthly_revenue',
                        'yearly_revenue',
                        'monthly_expenses',
                        'yearly_expenses',
                        'monthly_profit',
                        'yearly_profit',
                        'profit_margin',
                        'cash_balance',
                        'accounts_receivable',
                        'accounts_payable',
                    ],
                    'timestamp',
                ],
            ]);
    }

    public function test_kpis_endpoint_returns_all_kpis()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/kpis');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'financial',
                    'project',
                    'operational',
                    'hr',
                ],
                'timestamp',
            ]);
    }

    public function test_chart_endpoint_returns_revenue_trend()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/charts/revenue-trend');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'datasets',
                ],
            ]);
    }

    public function test_chart_endpoint_returns_project_status()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/charts/project-status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'datasets',
                ],
            ]);
    }

    public function test_chart_endpoint_returns_error_for_invalid_type()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/charts/invalid-type');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_save_layout_creates_dashboard_layout()
    {
        Sanctum::actingAs($this->user);

        $layoutData = [
            'dashboard_type' => 'executive',
            'layout_config' => [
                'widgets' => [
                    ['id' => 1, 'position' => 'top-left'],
                    ['id' => 2, 'position' => 'top-right'],
                ],
            ],
        ];

        $response = $this->postJson('/api/dashboard/save-layout', $layoutData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard layout saved successfully',
            ]);

        $this->assertDatabaseHas('dashboard_layouts', [
            'user_id' => $this->user->id,
            'dashboard_type' => 'executive',
        ]);
    }

    public function test_save_layout_updates_existing_layout()
    {
        Sanctum::actingAs($this->user);

        // Create initial layout
        $this->postJson('/api/dashboard/save-layout', [
            'dashboard_type' => 'executive',
            'layout_config' => ['widgets' => []],
        ]);

        // Update layout
        $updatedLayout = [
            'dashboard_type' => 'executive',
            'layout_config' => [
                'widgets' => [
                    ['id' => 1, 'position' => 'bottom-left'],
                ],
            ],
        ];

        $response = $this->postJson('/api/dashboard/save-layout', $updatedLayout);

        $response->assertStatus(200);

        // Should still have only one record
        $this->assertEquals(1, \App\Models\DashboardLayout::where('user_id', $this->user->id)
            ->where('dashboard_type', 'executive')
            ->count());
    }

    public function test_project_spi_calculation()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 1000000,
            'planned_value' => 500000,
            'earned_value' => 400000,
            'actual_cost' => 450000,
            'progress' => 50,
        ]);

        // SPI = EV / PV = 400000 / 500000 = 0.8
        $this->assertEquals(0.8, $project->spi);
    }

    public function test_project_cpi_calculation()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'budget' => 1000000,
            'planned_value' => 500000,
            'earned_value' => 400000,
            'actual_cost' => 450000,
            'progress' => 50,
        ]);

        // CPI = EV / AC = 400000 / 450000 = 0.89
        $this->assertEquals(0.89, $project->cpi);
    }
}
