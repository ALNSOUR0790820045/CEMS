<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PoReceipt;
use App\Models\PoReceiptItem;
use App\Models\PoAmendment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $vendor;
    protected $warehouse;
    protected $material;
    protected $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->vendor = Vendor::create([
            'name' => 'Test Vendor',
            'code' => 'VEND001',
            'email' => 'vendor@test.com',
            'company_id' => $this->company->id,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Test Warehouse',
            'code' => 'WH001',
            'location' => 'Test Location',
            'company_id' => $this->company->id,
        ]);

        $this->material = Material::create([
            'name' => 'Test Material',
            'code' => 'MAT001',
            'unit' => 'KG',
            'standard_cost' => 100,
            'company_id' => $this->company->id,
        ]);

        $this->unit = Unit::firstOrCreate(['name' => 'KG']);
    }

    /** @test */
    public function it_can_create_a_purchase_order()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/purchase-orders', [
                'po_date' => '2026-01-07',
                'vendor_id' => $this->vendor->id,
                'notes' => 'Test PO',
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'description' => 'Test Material',
                        'quantity' => 10,
                        'unit_price' => 100,
                        'discount_percentage' => 0,
                        'tax_percentage' => 5,
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'po_number',
                    'status',
                    'total_amount'
                ]
            ]);

        $this->assertDatabaseHas('purchase_orders', [
            'vendor_id' => $this->vendor->id,
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function it_can_list_purchase_orders()
    {
        PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'draft',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/purchase-orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'po_number',
                        'status'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_approve_a_purchase_order()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'draft',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/purchase-orders/{$po->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function it_can_create_a_partial_receipt()
    {
        // Create a PO with items
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'sent',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'material_id' => $this->material->id,
            'description' => 'Test Material',
            'quantity' => 100,
            'unit_price' => 10,
            'total_price' => 1000,
        ]);

        // Receive partial quantity
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/po-receipts', [
                'purchase_order_id' => $po->id,
                'receipt_date' => '2026-01-08',
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'po_item_id' => $poItem->id,
                        'quantity_received' => 50, // Partial receipt
                    ]
                ]
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('po_receipts', [
            'purchase_order_id' => $po->id,
            'status' => 'pending_inspection',
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $poItem->id,
            'quantity_received' => 50,
        ]);

        // Check PO status updated to partially_received
        $po->refresh();
        $this->assertEquals('partially_received', $po->status);
    }

    /** @test */
    public function it_cannot_receive_more_than_ordered_quantity()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'sent',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'material_id' => $this->material->id,
            'description' => 'Test Material',
            'quantity' => 100,
            'unit_price' => 10,
            'total_price' => 1000,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/po-receipts', [
                'purchase_order_id' => $po->id,
                'receipt_date' => '2026-01-08',
                'warehouse_id' => $this->warehouse->id,
                'items' => [
                    [
                        'po_item_id' => $poItem->id,
                        'quantity_received' => 150, // More than ordered
                    ]
                ]
            ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function it_can_create_an_amendment()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'approved',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/po-amendments', [
                'purchase_order_id' => $po->id,
                'amendment_date' => '2026-01-08',
                'amendment_type' => 'quantity',
                'description' => 'Increase quantity',
                'old_value' => '100',
                'new_value' => '150',
                'reason' => 'Additional requirement',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('po_amendments', [
            'purchase_order_id' => $po->id,
            'amendment_type' => 'quantity',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_can_approve_an_amendment()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'approved',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $amendment = PoAmendment::create([
            'amendment_number' => 'AMD-2026-0001',
            'purchase_order_id' => $po->id,
            'amendment_date' => '2026-01-08',
            'amendment_type' => 'quantity',
            'description' => 'Increase quantity',
            'status' => 'pending',
            'requested_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/po-amendments/{$amendment->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('po_amendments', [
            'id' => $amendment->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function it_can_inspect_a_receipt()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => '2026-01-07',
            'vendor_id' => $this->vendor->id,
            'status' => 'sent',
            'total_amount' => 1000,
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'material_id' => $this->material->id,
            'description' => 'Test Material',
            'quantity' => 100,
            'unit_price' => 10,
            'total_price' => 1000,
        ]);

        $receipt = PoReceipt::create([
            'receipt_number' => 'RCV-2026-0001',
            'purchase_order_id' => $po->id,
            'receipt_date' => '2026-01-08',
            'received_by_id' => $this->user->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'pending_inspection',
            'company_id' => $this->company->id,
        ]);

        $receiptItem = PoReceiptItem::create([
            'po_receipt_id' => $receipt->id,
            'po_item_id' => $poItem->id,
            'quantity_received' => 100,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/po-receipts/{$receipt->id}/inspect", [
                'items' => [
                    [
                        'id' => $receiptItem->id,
                        'quantity_accepted' => 95,
                        'quantity_rejected' => 5,
                        'rejection_reason' => 'Quality issues',
                    ]
                ]
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('po_receipt_items', [
            'id' => $receiptItem->id,
            'quantity_accepted' => 95,
            'quantity_rejected' => 5,
        ]);

        $this->assertDatabaseHas('po_receipts', [
            'id' => $receipt->id,
            'status' => 'inspected',
        ]);
    }
}
