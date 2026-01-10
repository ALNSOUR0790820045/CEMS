<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_user_can_list_contracts(): void
    {
        Contract::factory()->count(3)->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/contracts');

        $response->assertStatus(200);
    }

    public function test_user_can_create_contract(): void
    {
        $contractData = [
            'project_id' => $this->project->id,
            'contract_number' => 'CNT-001',
            'title' => 'Test Contract',
            'description' => 'Test contract description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'contract_value' => 2000000,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->post('/contracts', $contractData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('contracts', [
            'contract_number' => 'CNT-001',
            'title' => 'Test Contract',
        ]);
    }

    public function test_contract_requires_project_id(): void
    {
        $contractData = [
            'contract_number' => 'CNT-001',
            'title' => 'Test Contract',
            // project_id is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/contracts', $contractData);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_user_can_view_contract(): void
    {
        $contract = Contract::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/contracts/{$contract->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_contract(): void
    {
        $contract = Contract::factory()->create([
            'project_id' => $this->project->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/contracts/{$contract->id}", [
                'project_id' => $this->project->id,
                'contract_number' => $contract->contract_number,
                'title' => 'Updated Title',
                'start_date' => $contract->start_date->format('Y-m-d'),
                'end_date' => $contract->end_date->format('Y-m-d'),
                'contract_value' => $contract->contract_value,
                'status' => $contract->status,
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_contract_belongs_to_project(): void
    {
        $contract = Contract::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $this->assertInstanceOf(Project::class, $contract->project);
        $this->assertEquals($this->project->id, $contract->project->id);
    }
}
