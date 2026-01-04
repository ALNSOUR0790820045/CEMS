<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseRequisition;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequisitionTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $employee;
    protected $unit;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'JO',
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->employee = Employee::create([
            'employee_number' => 'EMP001',
            'name' => 'Test Employee',
            'company_id' => $this->company->id,
        ]);

        $this->unit = Unit::create([
            'name' => 'Piece',
            'code' => 'PC',
            'symbol' => 'pc',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_purchase_requisition(): void
    {
        $data = [
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'company_id' => $this->company->id,
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 10,
                    'unit_id' => $this->unit->id,
                    'estimated_unit_price' => 100.00,
                ]
            ]
        ];

        $response = $this->postJson('/api/purchase-requisitions', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'pr_number',
                    'pr_date',
                    'required_date',
                    'status',
                ]
            ])
            ->assertJson([
                'message' => 'Purchase requisition created successfully',
            ]);
    }

    public function test_purchase_requisition_auto_generates_pr_number(): void
    {
        $pr = PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $this->assertNotNull($pr->pr_number);
        $this->assertMatchesRegularExpression('/^PR-\d{4}-\d{4}$/', $pr->pr_number);
    }

    public function test_can_approve_purchase_requisition(): void
    {
        $pr = PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'status' => 'submitted',
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson("/api/purchase-requisitions/{$pr->id}/approve", [
            'approved_by_id' => $this->user->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Purchase requisition approved successfully',
            ]);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $pr->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_can_reject_purchase_requisition(): void
    {
        $pr = PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'status' => 'submitted',
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson("/api/purchase-requisitions/{$pr->id}/reject", [
            'approved_by_id' => $this->user->id,
            'rejection_reason' => 'Budget constraints',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Purchase requisition rejected successfully',
            ]);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $pr->id,
            'status' => 'rejected',
            'rejection_reason' => 'Budget constraints',
        ]);
    }

    public function test_can_list_purchase_requisitions(): void
    {
        PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/purchase-requisitions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'pr_number',
                        'pr_date',
                        'status',
                    ]
                ]
            ]);
    }

    public function test_can_filter_purchase_requisitions_by_status(): void
    {
        PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/purchase-requisitions?status=draft');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $pr) {
            $this->assertEquals('draft', $pr['status']);
        }
    }

    public function test_item_total_is_calculated_automatically(): void
    {
        $pr = PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $item = $pr->items()->create([
            'description' => 'Test Item',
            'quantity' => 10,
            'unit_id' => $this->unit->id,
            'estimated_unit_price' => 100.00,
        ]);

        $this->assertEquals(1000.00, $item->fresh()->estimated_total);
    }

    public function test_pr_total_amount_is_updated_when_items_change(): void
    {
        $pr = PurchaseRequisition::create([
            'pr_date' => '2026-01-04',
            'required_date' => '2026-01-15',
            'requested_by_id' => $this->employee->id,
            'priority' => 'normal',
            'company_id' => $this->company->id,
        ]);

        $pr->items()->create([
            'description' => 'Item 1',
            'quantity' => 10,
            'unit_id' => $this->unit->id,
            'estimated_unit_price' => 100.00,
        ]);

        $pr->items()->create([
            'description' => 'Item 2',
            'quantity' => 5,
            'unit_id' => $this->unit->id,
            'estimated_unit_price' => 200.00,
        ]);

        $this->assertEquals(2000.00, $pr->fresh()->total_amount);
    }
}
