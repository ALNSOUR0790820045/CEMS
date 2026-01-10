<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_project_summary(): void
    {
        $response = $this->getJson('/api/analytics/project-summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_projects',
                     'active_projects',
                     'completed_projects',
                     'total_budget',
                     'total_spent',
                 ]);
    }

    public function test_can_get_financial_overview(): void
    {
        $response = $this->getJson('/api/analytics/financial-overview');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_revenue',
                     'total_expenses',
                     'accounts_receivable',
                     'accounts_payable',
                     'net_profit',
                 ]);
    }

    public function test_can_get_revenue_trend(): void
    {
        $response = $this->getJson('/api/analytics/revenue-trend');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'labels',
                     'data',
                 ]);
    }

    public function test_can_get_revenue_trend_with_custom_months(): void
    {
        $response = $this->getJson('/api/analytics/revenue-trend?months=6');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'labels',
                     'data',
                 ]);
    }

    public function test_can_get_expense_breakdown(): void
    {
        $response = $this->getJson('/api/analytics/expense-breakdown');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'labels',
                     'data',
                 ]);
    }

    public function test_can_get_cash_position(): void
    {
        $response = $this->getJson('/api/analytics/cash-position');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'cash_inflow',
                     'cash_outflow',
                     'net_cash_flow',
                     'bank_balances',
                 ]);
    }

    public function test_can_get_project_performance(): void
    {
        $response = $this->getJson('/api/analytics/project-performance');

        $response->assertStatus(200)
                 ->assertJsonIsArray();
    }

    public function test_can_get_hr_metrics(): void
    {
        $response = $this->getJson('/api/analytics/hr-metrics');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_employees',
                     'active_employees',
                     'departments_count',
                     'department_distribution',
                 ]);
    }
}
