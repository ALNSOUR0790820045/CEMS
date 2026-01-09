<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\InventoryBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MaterialRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $department;
    protected $material;
    protected $unit;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->department = Department::create([
            'name' => 'Engineering',
            'code' => 'ENG001',
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'name' => 'Test Project',
            'project_code' => 'PROJ001',
            'client_id' => \App\Models\Client::factory()->create()->id,
            'contract_value' => 1000000,
            'contract_currency_id' => \App\Models\Currency::factory()->create()->id,
            'contract_start_date' => now(),
            'contract_end_date' => now()->addMonths(12),
            'contract_duration_days' => 365,
            'project_manager_id' => $this->user->id,
            'company_id' => $this->company->id,
            'project_status' => 'execution',
            'is_active' => true,
        ]);

        $this->unit = Unit::create([
            'name' => 'Piece',
            'symbol' => 'pc',
            'is_active' => true,
        ]);

        $this->material = Material::create([
            'name' => 'Test Material',
            'code' => 'MAT001',
            'unit' => 'piece',
            'standard_cost' => 100,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'code' => 'WH001',
            'address' => 'Test Location',
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);
    }

    public function test_can_create_material_request(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/material-requests', [
                'request_date' => now()->toDateString(),
                'project_id' => $this->project->id,
                'department_id' => $this->department->id,
                'priority' => 'high',
                'required_date' => now()->addDays(7)->toDateString(),
                'status' => 'draft',
                'notes' => 'Test material request',
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'description' => 'Test item',
                        'quantity_requested' => 10,
                        'unit_id' => $this->unit->id,
                        'unit_price' => 100,
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'request_number',
                    'status',
                    'items',
                ],
            ]);

        $this->assertDatabaseHas('material_requests', [
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'priority' => 'high',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('material_request_items', [
            'material_id' => $this->material->id,
            'quantity_requested' => 10,
        ]);
    }

    public function test_can_list_material_requests(): void
    {
        MaterialRequest::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/material-requests');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'request_number',
                        'status',
                        'priority',
                    ],
                ],
            ]);
    }

    public function test_can_show_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        MaterialRequestItem::create([
            'material_request_id' => $materialRequest->id,
            'material_id' => $this->material->id,
            'quantity_requested' => 5,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/material-requests/{$materialRequest->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'request_number',
                'items' => [
                    '*' => [
                        'material_id',
                        'quantity_requested',
                    ],
                ],
            ]);
    }

    public function test_can_update_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/material-requests/{$materialRequest->id}", [
                'priority' => 'urgent',
                'notes' => 'Updated notes',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Material request updated successfully',
            ]);

        $this->assertDatabaseHas('material_requests', [
            'id' => $materialRequest->id,
            'priority' => 'urgent',
        ]);
    }

    public function test_can_delete_draft_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/material-requests/{$materialRequest->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('material_requests', ['id' => $materialRequest->id]);
    }

    public function test_cannot_delete_approved_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/material-requests/{$materialRequest->id}");

        $response->assertStatus(422);
    }

    public function test_can_approve_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'pending_approval',
            'company_id' => $this->company->id,
        ]);

        MaterialRequestItem::create([
            'material_request_id' => $materialRequest->id,
            'material_id' => $this->material->id,
            'quantity_requested' => 10,
            'unit_id' => $this->unit->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/material-requests/{$materialRequest->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Material request approved successfully',
            ]);

        $this->assertDatabaseHas('material_requests', [
            'id' => $materialRequest->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('material_request_items', [
            'material_request_id' => $materialRequest->id,
            'quantity_approved' => 10,
        ]);
    }

    public function test_can_reject_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'pending_approval',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/material-requests/{$materialRequest->id}/reject", [
                'rejection_reason' => 'Not enough budget',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Material request rejected successfully',
            ]);

        $this->assertDatabaseHas('material_requests', [
            'id' => $materialRequest->id,
            'status' => 'rejected',
            'rejection_reason' => 'Not enough budget',
        ]);
    }

    public function test_can_issue_materials(): void
    {
        // Create material request
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $item = MaterialRequestItem::create([
            'material_request_id' => $materialRequest->id,
            'material_id' => $this->material->id,
            'quantity_requested' => 10,
            'quantity_approved' => 10,
            'unit_id' => $this->unit->id,
            'unit_price' => 100,
        ]);

        // Create inventory balance
        InventoryBalance::create([
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'average_cost' => 100,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/material-requests/{$materialRequest->id}/issue", [
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'id' => $item->id,
                        'quantity_issued' => 5,
                    ],
                ],
                'notes' => 'Partial issuance',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Materials issued successfully',
            ]);

        $this->assertDatabaseHas('material_request_items', [
            'id' => $item->id,
            'quantity_issued' => 5,
        ]);

        $this->assertDatabaseHas('material_requests', [
            'id' => $materialRequest->id,
            'status' => 'partially_issued',
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'material_id' => $this->material->id,
            'transaction_type' => 'issue',
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('inventory_balances', [
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 95, // 100 - 5
        ]);
    }

    public function test_validates_quantity_when_issuing(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_number' => 'MR-2026-0001',
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $item = MaterialRequestItem::create([
            'material_request_id' => $materialRequest->id,
            'material_id' => $this->material->id,
            'quantity_requested' => 10,
            'quantity_approved' => 10,
            'unit_id' => $this->unit->id,
        ]);

        // Create inventory balance with insufficient quantity
        InventoryBalance::create([
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 5,
            'average_cost' => 100,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/material-requests/{$materialRequest->id}/issue", [
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'id' => $item->id,
                        'quantity_issued' => 10, // More than available
                    ],
                ],
            ]);

        $response->assertStatus(500)
            ->assertJsonStructure(['message', 'error']);
    }

    public function test_generates_unique_request_number(): void
    {
        $requestNumber1 = MaterialRequest::generateRequestNumber();
        
        MaterialRequest::create([
            'request_number' => $requestNumber1,
            'request_date' => now(),
            'project_id' => $this->project->id,
            'department_id' => $this->department->id,
            'requested_by_id' => $this->user->id,
            'priority' => 'medium',
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $requestNumber2 = MaterialRequest::generateRequestNumber();
        
        $this->assertNotEquals($requestNumber1, $requestNumber2);
        $this->assertStringContainsString('MR-' . date('Y'), $requestNumber1);
        $this->assertStringContainsString('MR-' . date('Y'), $requestNumber2);
    }
}
