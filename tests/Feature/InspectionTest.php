<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\InspectionType;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $inspectionType;
    protected $inspector;

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

        $this->inspector = User::create([
            'name' => 'Inspector User',
            'email' => 'inspector@example.com',
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

    public function test_can_create_inspection(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'inspection_date' => now()->format('Y-m-d'),
            'inspection_time' => '10:00',
            'location' => 'Building A',
            'work_area' => 'Foundation',
            'description' => 'Foundation concrete inspection',
            'inspector_id' => $this->inspector->id,
            'contractor_rep' => 'John Doe',
            'consultant_rep' => 'Jane Smith',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/inspections', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection created successfully',
            ]);

        $this->assertDatabaseHas('inspections', [
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'location' => 'Building A',
            'status' => 'draft',
        ]);

        $inspection = Inspection::first();
        $this->assertStringStartsWith('INS-', $inspection->inspection_number);
    }

    public function test_can_save_inspection_items(): void
    {
        $inspection = Inspection::create([
            'inspection_number' => 'INS-2026-0001',
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'inspection_date' => now(),
            'inspector_id' => $this->inspector->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $itemsData = [
            'items' => [
                [
                    'item_description' => 'Concrete quality check',
                    'acceptance_criteria' => 'No cracks or defects',
                    'result' => 'pass',
                    'score' => 100,
                    'requires_action' => false,
                ],
                [
                    'item_description' => 'Reinforcement spacing',
                    'acceptance_criteria' => 'As per drawings',
                    'result' => 'fail',
                    'score' => 60,
                    'remarks' => 'Spacing not uniform',
                    'requires_action' => true,
                ],
            ],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspections/{$inspection->id}/items", $itemsData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection items saved successfully',
            ]);

        $this->assertDatabaseHas('inspection_items', [
            'inspection_id' => $inspection->id,
            'item_description' => 'Concrete quality check',
            'result' => 'pass',
        ]);

        $this->assertDatabaseHas('inspection_items', [
            'inspection_id' => $inspection->id,
            'item_description' => 'Reinforcement spacing',
            'result' => 'fail',
        ]);

        // Check that inspection was updated with calculated values
        $inspection->refresh();
        $this->assertEquals('fail', $inspection->result);
        $this->assertEquals(1, $inspection->defects_found);
        $this->assertEquals(80, $inspection->overall_score); // (100 + 60) / 2
    }

    public function test_can_submit_and_approve_inspection(): void
    {
        $inspection = Inspection::create([
            'inspection_number' => 'INS-2026-0002',
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'inspection_date' => now(),
            'inspector_id' => $this->inspector->id,
            'status' => 'draft',
            'result' => 'pass',
            'company_id' => $this->company->id,
        ]);

        // Submit inspection
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspections/{$inspection->id}/submit");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection submitted successfully',
            ]);

        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'status' => 'submitted',
        ]);

        // Approve inspection
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspections/{$inspection->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Inspection approved successfully',
            ]);

        $this->assertDatabaseHas('inspections', [
            'id' => $inspection->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);

        $inspection->refresh();
        $this->assertNotNull($inspection->approved_at);
    }

    public function test_can_create_reinspection(): void
    {
        $originalInspection = Inspection::create([
            'inspection_number' => 'INS-2026-0003',
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'inspection_date' => now()->subDays(5),
            'inspector_id' => $this->inspector->id,
            'location' => 'Building A',
            'work_area' => 'Foundation',
            'result' => 'fail',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/inspections/{$originalInspection->id}/reinspect", [
                'inspection_date' => now()->format('Y-m-d'),
                'inspection_time' => '14:00',
                'inspector_id' => $this->inspector->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Reinspection created successfully',
            ]);

        $this->assertDatabaseHas('inspections', [
            'reinspection_of_id' => $originalInspection->id,
            'project_id' => $this->project->id,
            'inspection_type_id' => $this->inspectionType->id,
            'status' => 'draft',
        ]);
    }
}
