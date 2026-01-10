<?php

namespace Tests\Feature;

use App\Models\ActualCost;
use App\Models\Project;
use App\Models\ProjectBudget;
use App\Models\ProjectBudgetItem;
use App\Models\CostCode;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\Vendor;
use App\Models\VarianceAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VarianceAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected Currency $currency;
    protected CostCode $costCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create();
        $this->costCode = CostCode::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    /**
     * Test variance calculation.
     */
    public function test_variance_is_calculated_correctly(): void
    {
        $analysisData = [
            'project_id' => $this->project->id,
            'analysis_date' => now()->format('Y-m-d'),
            'period_month' => now()->month,
            'period_year' => now()->year,
            'cost_code_id' => $this->costCode->id,
            'budgeted_amount' => 100000,
            'actual_amount' => 110000,
        ];

        $response = $this->postJson('/api/variance-analysis', $analysisData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'variance_amount' => -10000,
                     'variance_percentage' => -10.0,
                     'variance_type' => 'unfavorable',
                 ]);
    }

    /**
     * Test automatic variance analysis for project.
     */
    public function test_can_analyze_project_variances(): void
    {
        // Create active budget with items
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
        ]);

        $budgetItem = ProjectBudgetItem::factory()->create([
            'project_budget_id' => $budget->id,
            'cost_code_id' => $this->costCode->id,
            'budgeted_amount' => 100000,
        ]);

        // Create actual costs that exceed budget by more than 5%
        $vendor = Vendor::factory()->create(['company_id' => $this->company->id]);
        
        ActualCost::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'amount_local' => 120000,
            'company_id' => $this->company->id,
            'transaction_date' => now(),
            'posted_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'vendor_id' => $vendor->id,
        ]);

        $response = $this->postJson("/api/variance-analysis/analyze/{$this->project->id}", [
            'period_month' => now()->month,
            'period_year' => now()->year,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'analyses_created',
                     'data',
                 ]);

        $this->assertDatabaseHas('variance_analysis', [
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'status' => 'identified',
        ]);
    }

    /**
     * Test favorable variance is correctly identified.
     */
    public function test_favorable_variance_is_identified(): void
    {
        $analysisData = [
            'project_id' => $this->project->id,
            'analysis_date' => now()->format('Y-m-d'),
            'period_month' => now()->month,
            'period_year' => now()->year,
            'cost_code_id' => $this->costCode->id,
            'budgeted_amount' => 100000,
            'actual_amount' => 85000,
        ];

        $response = $this->postJson('/api/variance-analysis', $analysisData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'variance_amount' => 15000,
                     'variance_type' => 'favorable',
                 ]);
    }

    /**
     * Test can update variance analysis status.
     */
    public function test_can_update_variance_status(): void
    {
        $analysis = VarianceAnalysis::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'status' => 'identified',
        ]);

        $response = $this->putJson("/api/variance-analysis/{$analysis->id}", [
            'status' => 'analyzed',
            'variance_reason' => 'Material price increase',
            'corrective_action' => 'Negotiate with supplier',
            'responsible_person_id' => $this->user->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('variance_analysis', [
            'id' => $analysis->id,
            'status' => 'analyzed',
            'variance_reason' => 'Material price increase',
        ]);
    }
}
