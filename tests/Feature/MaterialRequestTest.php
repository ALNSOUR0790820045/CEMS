<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->department = Department::create([
            'name' => 'Test Department',
            'code' => 'TEST-DEPT',
            'company_id' => $this->company->id,
        ]);
        $this->employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'company_id' => $this->company->id,
            'department_id' => $this->department->id,
        ]);
        $this->material = Material::create([
            'name' => 'Test Material',
            'code' => 'TEST-MAT',
            'unit' => 'pcs',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_material_request(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/material-requests', [
                'request_date' => now()->format('Y-m-d'),
                'required_date' => now()->addDays(7)->format('Y-m-d'),
                'requested_by_id' => $this->employee->id,
                'department_id' => $this->department->id,
                'request_type' => 'from_warehouse',
                'priority' => 'normal',
                'company_id' => $this->company->id,
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'requested_quantity' => 10,
                        'purpose' => 'Test purpose',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('material_requests', [
            'requested_by_id' => $this->employee->id,
            'status' => 'draft',
        ]);
        
        $this->assertDatabaseHas('material_request_items', [
            'material_id' => $this->material->id,
            'requested_quantity' => 10,
        ]);
    }

    public function test_can_approve_material_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_date' => now(),
            'required_date' => now()->addDays(7),
            'requested_by_id' => $this->employee->id,
            'department_id' => $this->department->id,
            'request_type' => 'from_warehouse',
            'priority' => 'normal',
            'status' => 'submitted',
            'company_id' => $this->company->id,
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
    }

    public function test_can_list_material_requests(): void
    {
        MaterialRequest::create([
            'request_date' => now(),
            'required_date' => now()->addDays(7),
            'requested_by_id' => $this->employee->id,
            'request_type' => 'from_warehouse',
            'priority' => 'normal',
            'status' => 'draft',
            'company_id' => $this->company->id,
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

    public function test_cannot_delete_non_draft_request(): void
    {
        $materialRequest = MaterialRequest::create([
            'request_date' => now(),
            'required_date' => now()->addDays(7),
            'requested_by_id' => $this->employee->id,
            'request_type' => 'from_warehouse',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/material-requests/{$materialRequest->id}");

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Only draft requests can be deleted',
            ]);
    }
}
