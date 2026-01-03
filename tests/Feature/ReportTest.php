<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::create([
            'name' => 'Test Company',
            'name_en' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'US',
            'is_active' => true,
        ]);
        
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_can_access_cost_analysis_report(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/reports/cost-analysis');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    public function test_can_access_budget_variance_report(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/reports/budget-variance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    public function test_can_access_cost_center_report(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/reports/cost-center-report');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }
}
