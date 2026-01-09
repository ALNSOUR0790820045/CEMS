<?php

namespace Tests\Feature;

use App\Models\ProjectBudget;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectBudgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create();
        
        Sanctum::actingAs($this->user);
    }

    /**
     * Test can create a project budget.
     */
    public function test_can_create_project_budget(): void
    {
        $budgetData = [
            'project_id' => $this->project->id,
            'budget_type' => 'original',
            'budget_date' => now()->format('Y-m-d'),
            'contract_value' => 1000000,
            'direct_costs' => 700000,
            'indirect_costs' => 200000,
            'contingency_percentage' => 10,
            'profit_margin_percentage' => 15,
            'currency_id' => $this->currency->id,
            'notes' => 'Test budget',
        ];

        $response = $this->postJson('/api/project-budgets', $budgetData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'budget_number',
                     'project_id',
                     'budget_type',
                     'total_budget',
                     'status',
                 ]);

        $this->assertDatabaseHas('project_budgets', [
            'project_id' => $this->project->id,
            'budget_type' => 'original',
            'status' => 'draft',
        ]);
    }

    /**
     * Test can list project budgets.
     */
    public function test_can_list_project_budgets(): void
    {
        ProjectBudget::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/project-budgets');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'budget_number', 'project_id', 'status']
                     ]
                 ]);
    }

    /**
     * Test can get a specific project budget.
     */
    public function test_can_show_project_budget(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/project-budgets/{$budget->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $budget->id,
                     'budget_number' => $budget->budget_number,
                 ]);
    }

    /**
     * Test can approve a project budget.
     */
    public function test_can_approve_project_budget(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/project-budgets/{$budget->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('project_budgets', [
            'id' => $budget->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    /**
     * Test cannot update approved budget.
     */
    public function test_cannot_update_approved_budget(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'approved',
        ]);

        $response = $this->putJson("/api/project-budgets/{$budget->id}", [
            'notes' => 'Updated notes',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'error' => 'Cannot update approved or active budget'
                 ]);
    }

    /**
     * Test can create revised budget.
     */
    public function test_can_create_revised_budget(): void
    {
        $originalBudget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'approved',
            'version' => 1,
        ]);

        $response = $this->postJson("/api/project-budgets/{$originalBudget->id}/revise", [
            'copy_items' => false,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'budget_type' => 'revised',
                     'version' => 2,
                     'status' => 'draft',
                 ]);
    }

    /**
     * Test budget number generation.
     */
    public function test_budget_number_is_generated(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $this->assertMatchesRegularExpression('/BUD-\d{4}-\d{4}/', $budget->budget_number);
    }
}
