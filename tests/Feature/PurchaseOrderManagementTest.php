<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->supplier = Supplier::create([
            'company_id' => $this->company->id,
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'is_active' => true,
        ]);
    }

    public function test_user_can_list_purchase_orders(): void
    {
        PurchaseOrder::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/purchase-orders');

        $response->assertStatus(200);
    }

    public function test_user_can_create_purchase_order(): void
    {
        $poData = [
            'project_id' => $this->project->id,
            'supplier_id' => $this->supplier->id,
            'po_number' => 'PO-001',
            'order_date' => '2026-01-01',
            'expected_delivery_date' => '2026-01-15',
            'status' => 'draft',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
        ];

        $response = $this->actingAs($this->user)
            ->post('/purchase-orders', $poData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('purchase_orders', [
            'po_number' => 'PO-001',
        ]);
    }

    public function test_purchase_order_requires_supplier(): void
    {
        $poData = [
            'project_id' => $this->project->id,
            'po_number' => 'PO-001',
            // supplier_id is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/purchase-orders', $poData);

        $response->assertSessionHasErrors(['supplier_id']);
    }

    public function test_user_can_view_purchase_order(): void
    {
        $po = PurchaseOrder::factory()->create([
            'project_id' => $this->project->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/purchase-orders/{$po->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_purchase_order_status(): void
    {
        $po = PurchaseOrder::factory()->create([
            'project_id' => $this->project->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/purchase-orders/{$po->id}", [
                'project_id' => $this->project->id,
                'supplier_id' => $this->supplier->id,
                'po_number' => $po->po_number,
                'order_date' => $po->order_date->format('Y-m-d'),
                'status' => 'approved',
                'subtotal' => $po->subtotal,
                'total_amount' => $po->total_amount,
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'approved',
        ]);
    }

    public function test_purchase_order_calculates_total_correctly(): void
    {
        $po = PurchaseOrder::factory()->create([
            'project_id' => $this->project->id,
            'supplier_id' => $this->supplier->id,
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'discount' => 500,
        ]);

        // Total should be subtotal + tax - discount
        $expectedTotal = 10000 + 1500 - 500;
        
        $this->assertEquals($expectedTotal, $po->total_amount);
    }
}
