<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Material;
use App\Models\Vendor;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\PrQuote;
use Spatie\Permission\Models\Permission;

class PurchaseRequisitionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $currency;
    protected $department;
    protected $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // Create company
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'country' => 'US',
            'is_active' => true,
        ]);

        // Create user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        // Create currency
        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        // Create department
        $this->department = Department::create([
            'name' => 'Procurement',
            'code' => 'PROC',
            'company_id' => $this->company->id,
        ]);

        // Create unit
        $this->unit = Unit::create([
            'name' => 'Piece',
            'code' => 'PC',
            'company_id' => $this->company->id,
        ]);

        // Create permissions
        Permission::create(['name' => 'pr.view']);
        Permission::create(['name' => 'pr.create']);
        Permission::create(['name' => 'pr.approve']);
        
        $this->user->givePermissionTo(['pr.view', 'pr.create', 'pr.approve']);
    }

    public function test_can_create_purchase_requisition()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/purchase-requisitions', [
                'requisition_date' => now()->format('Y-m-d'),
                'required_date' => now()->addDays(30)->format('Y-m-d'),
                'department_id' => $this->department->id,
                'priority' => 'normal',
                'type' => 'materials',
                'currency_id' => $this->currency->id,
                'justification' => 'Need materials for project',
                'items' => [
                    [
                        'item_description' => 'Test Material',
                        'specifications' => 'High quality',
                        'quantity_requested' => 10,
                        'unit_id' => $this->unit->id,
                        'estimated_unit_price' => 100,
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'requisition_number',
                    'status',
                    'total_estimated_amount',
                ]
            ]);

        $this->assertDatabaseHas('purchase_requisitions', [
            'status' => 'draft',
            'total_estimated_amount' => 1000,
        ]);

        $this->assertDatabaseHas('purchase_requisition_items', [
            'item_description' => 'Test Material',
            'quantity_requested' => 10,
        ]);
    }

    public function test_can_list_purchase_requisitions()
    {
        // Create test requisitions
        PurchaseRequisition::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'requested_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/purchase-requisitions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'requisition_number',
                        'status',
                    ]
                ]
            ]);
    }

    public function test_can_submit_requisition_for_approval()
    {
        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'draft',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/purchase-requisitions/{$requisition->id}/submit");

        $response->assertStatus(200);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $requisition->id,
            'status' => 'pending_approval',
        ]);
    }

    public function test_can_approve_requisition()
    {
        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'pending_approval',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/purchase-requisitions/{$requisition->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $requisition->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_can_reject_requisition()
    {
        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'pending_approval',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/purchase-requisitions/{$requisition->id}/reject", [
                'reason' => 'Budget not available'
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $requisition->id,
            'status' => 'rejected',
            'rejection_reason' => 'Budget not available',
        ]);
    }

    public function test_can_create_quote()
    {
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'email' => 'vendor@example.com',
            'company_id' => $this->company->id,
        ]);

        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'approved',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $item = PurchaseRequisitionItem::create([
            'purchase_requisition_id' => $requisition->id,
            'item_description' => 'Test Item',
            'quantity_requested' => 10,
            'unit_id' => $this->unit->id,
            'estimated_unit_price' => 100,
            'estimated_total' => 1000,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/pr-quotes', [
                'purchase_requisition_id' => $requisition->id,
                'vendor_id' => $vendor->id,
                'quote_date' => now()->format('Y-m-d'),
                'validity_date' => now()->addDays(30)->format('Y-m-d'),
                'currency_id' => $this->currency->id,
                'items' => [
                    [
                        'pr_item_id' => $item->id,
                        'quantity' => 10,
                        'unit_price' => 95,
                        'discount_percentage' => 0,
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'quote_number',
                    'total_amount',
                ]
            ]);

        $this->assertDatabaseHas('pr_quotes', [
            'purchase_requisition_id' => $requisition->id,
            'vendor_id' => $vendor->id,
            'total_amount' => 950,
        ]);
    }

    public function test_can_select_quote()
    {
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'email' => 'vendor@example.com',
            'company_id' => $this->company->id,
        ]);

        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'approved',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $quote = PrQuote::create([
            'purchase_requisition_id' => $requisition->id,
            'vendor_id' => $vendor->id,
            'quote_date' => now(),
            'validity_date' => now()->addDays(30),
            'total_amount' => 950,
            'currency_id' => $this->currency->id,
            'status' => 'received',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/pr-quotes/{$quote->id}/select");

        $response->assertStatus(200);

        $this->assertDatabaseHas('pr_quotes', [
            'id' => $quote->id,
            'status' => 'selected',
        ]);
    }

    public function test_cannot_edit_submitted_requisition()
    {
        $requisition = PurchaseRequisition::create([
            'requisition_date' => now(),
            'required_date' => now()->addDays(30),
            'requested_by_id' => $this->user->id,
            'priority' => 'normal',
            'type' => 'materials',
            'status' => 'pending_approval',
            'total_estimated_amount' => 1000,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/purchase-requisitions/{$requisition->id}", [
                'priority' => 'high',
            ]);

        $response->assertStatus(403);
    }
}
