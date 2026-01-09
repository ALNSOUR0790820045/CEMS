<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InspectionRequest;
use App\Models\InspectionType;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $inspectionType;

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

        $this->inspectionType = InspectionType::create([
            'code' => 'IT-001',
            'name' => 'Structural Inspection',
            'category' => 'structural',
            'frequency' => 'once',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_inspection_request(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'request_date' => now()->format('Y-m-d'),
            'requested_date' => now()->addDays(3)->format('Y-m-d'),
            'requested_time' => '10:00',
            'location' => 'Building A',
            'work_area' => 'Foundation',
            'description' => 'Inspection of foundation concrete',
            'priority' => 'normal',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/inspection-requests', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection request created successfully',
            ]);

        $this->assertDatabaseHas('inspection_requests', [
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'location' => 'Building A',
            'status' => 'pending',
        ]);

        $request = InspectionRequest::first();
        $this->assertStringStartsWith('IR-', $request->request_number);
    }

    public function test_can_schedule_inspection_request(): void
    {
        $request = InspectionRequest::create([
            'request_number' => 'IR-2026-0001',
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'requested_by_id' => $this->user->id,
            'request_date' => now(),
            'requested_date' => now()->addDays(3),
            'status' => 'pending',
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $inspector = User::create([
            'name' => 'Inspector User',
            'email' => 'inspector@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspection-requests/{$request->id}/schedule", [
                'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
                'scheduled_time' => '14:00',
                'inspector_id' => $inspector->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection scheduled successfully',
            ]);

        $this->assertDatabaseHas('inspection_requests', [
            'id' => $request->id,
            'status' => 'scheduled',
            'inspector_id' => $inspector->id,
        ]);
    }

    public function test_can_reject_inspection_request(): void
    {
        $request = InspectionRequest::create([
            'request_number' => 'IR-2026-0002',
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'requested_by_id' => $this->user->id,
            'request_date' => now(),
            'requested_date' => now()->addDays(3),
            'status' => 'pending',
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspection-requests/{$request->id}/reject", [
                'rejection_reason' => 'Work not ready for inspection',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection request rejected',
            ]);

        $this->assertDatabaseHas('inspection_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'rejection_reason' => 'Work not ready for inspection',
        ]);
    }
}
