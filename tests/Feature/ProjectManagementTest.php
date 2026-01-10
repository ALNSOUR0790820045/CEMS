<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_user_can_list_projects(): void
    {
        Project::factory()->count(5)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/projects');

        $response->assertStatus(200);
    }

    public function test_user_can_create_project(): void
    {
        $projectData = [
            'company_id' => $this->company->id,
            'name' => 'Test Project',
            'code' => 'PROJ-001',
            'description' => 'Test project description',
            'location' => 'Test Location',
            'contract_value' => 1000000,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post('/projects', $projectData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('projects', [
            'code' => 'PROJ-001',
            'name' => 'Test Project',
        ]);
    }

    public function test_project_requires_name(): void
    {
        $projectData = [
            'company_id' => $this->company->id,
            'code' => 'PROJ-001',
            // name is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/projects', $projectData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_can_view_project(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$project->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_project(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/projects/{$project->id}", [
                'company_id' => $this->company->id,
                'name' => 'Updated Name',
                'code' => $project->code,
                'start_date' => $project->start_date->format('Y-m-d'),
                'end_date' => $project->end_date->format('Y-m-d'),
                'status' => $project->status,
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_delete_project(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$project->id}");

        $response->assertStatus(302);
        
        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }
}
