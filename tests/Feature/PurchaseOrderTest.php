<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $currency;
    protected $vendor;
    protected $unit;
    protected $material;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'US',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_active' => true,
        ]);

        $this->vendor = Vendor::create([
            'vendor_code' => 'V001',
            'name' => 'Test Vendor',
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->unit = Unit::create([
            'name' => 'Piece',
            'abbreviation' => 'PC',
            'company_id' => $this->company->id,
        ]);

        $this->material = Material::create([
            'material_code' => 'M001',
            'name' => 'Test Material',
            'unit_id' => $this->unit->id,
            'unit_price' => 100.00,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);
    }

    public function test_can_create_purchase_order()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/purchase-orders', [
            'po_date' => now()->format('Y-m-d'),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'quantity' => 10,
                    'unit_id' => $this->unit->id,
                    'unit_price' => 100.00,
                    'tax_rate' => 10,
                    'discount_rate' => 5,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'po_number',
                'status',
                'subtotal',
                'total_amount',
            ]
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'vendor_id' => $this->vendor->id,
            'status' => 'draft',
        ]);
    }

    public function test_can_list_purchase_orders()
    {
        $this->actingAs($this->user, 'sanctum');

        PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => now(),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/purchase-orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'po_number',
                    'status',
                ]
            ]
        ]);
    }

    public function test_can_show_purchase_order()
    {
        $this->actingAs($this->user, 'sanctum');

        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => now(),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson("/api/purchase-orders/{$po->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $po->id,
            'po_number' => $po->po_number,
        ]);
    }

    public function test_can_approve_purchase_order()
    {
        $this->actingAs($this->user, 'sanctum');

        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => now(),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->postJson("/api/purchase-orders/{$po->id}/approve");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Purchase order approved successfully',
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_calculates_totals_correctly()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/purchase-orders', [
            'po_date' => now()->format('Y-m-d'),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'quantity' => 10,
                    'unit_id' => $this->unit->id,
                    'unit_price' => 100.00,
                    'tax_rate' => 10,
                    'discount_rate' => 5,
                ]
            ]
        ]);

        $response->assertStatus(201);
        
        $po = PurchaseOrder::find($response->json('data.id'));
        
        // Base: 10 * 100 = 1000
        // Discount: 1000 * 0.05 = 50
        // After discount: 1000 - 50 = 950
        // Tax: 950 * 0.10 = 95
        // Line total: 950 + 95 = 1045
        
        $this->assertEquals(1045.00, $po->subtotal);
        $this->assertEquals(1045.00, $po->total_amount);
    }

    public function test_po_number_is_auto_generated()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/purchase-orders', [
            'po_date' => now()->format('Y-m-d'),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'quantity' => 10,
                    'unit_id' => $this->unit->id,
                    'unit_price' => 100.00,
                ]
            ]
        ]);

        $response->assertStatus(201);
        
        $poNumber = $response->json('data.po_number');
        $this->assertMatchesRegularExpression('/^PO-\d{4}-\d{4}$/', $poNumber);
    }

    public function test_cannot_delete_non_draft_purchase_order()
    {
        $this->actingAs($this->user, 'sanctum');

        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => now(),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'approved',
        ]);

        $response = $this->deleteJson("/api/purchase-orders/{$po->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('purchase_orders', ['id' => $po->id]);
    }

    public function test_can_get_receiving_status()
    {
        $this->actingAs($this->user, 'sanctum');

        $po = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'po_date' => now(),
            'vendor_id' => $this->vendor->id,
            'delivery_date' => now()->addDays(7),
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'approved',
        ]);

        $po->items()->create([
            'material_id' => $this->material->id,
            'quantity' => 100,
            'unit_id' => $this->unit->id,
            'unit_price' => 100.00,
            'received_quantity' => 50,
        ]);

        $response = $this->getJson("/api/purchase-orders/{$po->id}/receiving-status");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'purchase_order_id',
            'po_number',
            'total_quantity',
            'received_quantity',
            'remaining_quantity',
            'receiving_percentage',
            'items',
        ]);
    }
}
