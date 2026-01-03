<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
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

    public function test_can_create_budget(): void
    {
        $data = [
            'budget_name' => '2024 Operating Budget',
            'fiscal_year' => 2024,
            'budget_type' => 'operating',
            'status' => 'draft',
            'total_budget' => 500000.00,
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/budgets', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'budget_name', 'fiscal_year', 'budget_type', 'status', 'total_budget']
            ]);

        $this->assertDatabaseHas('budgets', ['budget_name' => '2024 Operating Budget']);
    }

    public function test_validation_fails_for_invalid_budget_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/budgets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['budget_name', 'fiscal_year', 'budget_type', 'total_budget', 'company_id']);
    }
}
