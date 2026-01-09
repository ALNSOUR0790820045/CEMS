<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inspection;
use App\Models\InspectionAction;
use App\Models\InspectionItem;
use App\Models\InspectionType;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionActionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $inspection;
    protected $assignee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->assignee = User::create([
            'name' => 'Assignee User',
            'email' => 'assignee@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ-001',
            'company_id' => $this->company->id,
            'manager_id' => $this->user->id,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => 'active',
            'is_active' => true,
        ]);

        $inspectionType = InspectionType::create([
            'code' => 'IT-001',
            'name' => 'Structural Inspection',
            'category' => 'structural',
            'frequency' => 'once',
            'company_id' => $this->company->id,
        ]);

        $this->inspection = Inspection::create([
            'inspection_number' => 'INS-2026-0001',
            'project_id' => $this->project->id,
            'inspection_type_id' => $inspectionType->id,
            'inspection_date' => now(),
            'inspector_id' => $this->user->id,
            'result' => 'fail',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_corrective_action(): void
    {
        $data = [
            'inspection_id' => $this->inspection->id,
            'action_type' => 'corrective',
            'description' => 'Fix reinforcement spacing to match drawings',
            'assigned_to_id' => $this->assignee->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/inspections/' . $this->inspection->id . '/actions', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection action created successfully',
            ]);

        $this->assertDatabaseHas('inspection_actions', [
            'inspection_id' => $this->inspection->id,
            'action_type' => 'corrective',
            'description' => 'Fix reinforcement spacing to match drawings',
            'assigned_to_id' => $this->assignee->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_complete_action(): void
    {
        $action = InspectionAction::create([
            'inspection_id' => $this->inspection->id,
            'action_type' => 'corrective',
            'description' => 'Repair concrete defect',
            'assigned_to_id' => $this->assignee->id,
            'due_date' => now()->addDays(7),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->assignee, 'sanctum')
            ->postJson("/api/inspection-actions/{$action->id}/complete", [
                'completed_date' => now()->format('Y-m-d'),
                'remarks' => 'Repair completed successfully',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Action marked as completed',
            ]);

        $this->assertDatabaseHas('inspection_actions', [
            'id' => $action->id,
            'status' => 'completed',
            'remarks' => 'Repair completed successfully',
        ]);

        $action->refresh();
        $this->assertNotNull($action->completed_date);
    }

    public function test_can_verify_action(): void
    {
        $action = InspectionAction::create([
            'inspection_id' => $this->inspection->id,
            'action_type' => 'corrective',
            'description' => 'Repair concrete defect',
            'assigned_to_id' => $this->assignee->id,
            'due_date' => now()->addDays(7),
            'completed_date' => now(),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspection-actions/{$action->id}/verify");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Action verified successfully',
            ]);

        $this->assertDatabaseHas('inspection_actions', [
            'id' => $action->id,
            'status' => 'verified',
            'verified_by_id' => $this->user->id,
        ]);

        $action->refresh();
        $this->assertNotNull($action->verification_date);
    }

    public function test_can_list_actions_by_inspection(): void
    {
        InspectionAction::create([
            'inspection_id' => $this->inspection->id,
            'action_type' => 'corrective',
            'description' => 'Action 1',
            'assigned_to_id' => $this->assignee->id,
            'due_date' => now()->addDays(7),
            'status' => 'pending',
        ]);

        InspectionAction::create([
            'inspection_id' => $this->inspection->id,
            'action_type' => 'preventive',
            'description' => 'Action 2',
            'assigned_to_id' => $this->assignee->id,
            'due_date' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/inspections/{$this->inspection->id}/actions");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }
}
