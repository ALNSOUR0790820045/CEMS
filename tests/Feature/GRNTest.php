<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Material;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GRNTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $vendor;
    protected $warehouse;
    protected $material;

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
            'unit' => 'piece',
            'unit_price' => 100,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_grn(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/grns', [
                'grn_date' => now()->toDateString(),
                'vendor_id' => $this->vendor->id,
                'warehouse_id' => $this->warehouse->id,
                'delivery_note_number' => 'DN001',
                'items' => [
                    [
                        'material_id' => $this->material->id,
                        'received_quantity' => 10,
                        'unit_price' => 100,
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'grn_number',
                    'status',
                    'items',
                ],
            ]);

        $this->assertDatabaseHas('grns', [
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'received',
        ]);
    }

    public function test_can_list_grns(): void
    {
        GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/grns');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'grn_number',
                        'status',
                        'vendor',
                        'warehouse',
                    ],
                ],
            ]);
    }

    public function test_can_show_grn(): void
    {
        $grn = GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/grns/{$grn->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $grn->id,
                'grn_number' => $grn->grn_number,
            ]);
    }

    public function test_can_inspect_grn(): void
    {
        $grn = GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
            'status' => 'received',
        ]);

        $grnItem = GRNItem::create([
            'grn_id' => $grn->id,
            'material_id' => $this->material->id,
            'received_quantity' => 10,
            'unit_price' => 100,
            'total_amount' => 1000,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/grns/{$grn->id}/inspect", [
                'inspection_notes' => 'Quality check passed',
                'items' => [
                    [
                        'grn_item_id' => $grnItem->id,
                        'accepted_quantity' => 10,
                        'rejected_quantity' => 0,
                        'inspection_status' => 'passed',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'GRN inspected successfully',
            ]);

        $this->assertDatabaseHas('grns', [
            'id' => $grn->id,
            'status' => 'inspected',
        ]);

        $this->assertDatabaseHas('grn_items', [
            'id' => $grnItem->id,
            'accepted_quantity' => 10,
            'inspection_status' => 'passed',
        ]);
    }

    public function test_can_accept_grn_and_create_inventory_transaction(): void
    {
        $grn = GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
            'status' => 'inspected',
        ]);

        $grnItem = GRNItem::create([
            'grn_id' => $grn->id,
            'material_id' => $this->material->id,
            'received_quantity' => 10,
            'accepted_quantity' => 10,
            'unit_price' => 100,
            'total_amount' => 1000,
            'inspection_status' => 'passed',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/grns/{$grn->id}/accept");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'GRN accepted and inventory updated successfully',
            ]);

        $this->assertDatabaseHas('grns', [
            'id' => $grn->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'in',
            'reference_type' => 'GRN',
            'reference_id' => $grn->id,
            'quantity' => 10,
        ]);
    }

    public function test_can_get_pending_inspection_grns(): void
    {
        GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
            'status' => 'received',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/grns/pending-inspection');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'grn_number',
                    'status',
                ],
            ]);
    }

    public function test_cannot_update_grn_in_accepted_status(): void
    {
        $grn = GRN::factory()->create([
            'company_id' => $this->company->id,
            'vendor_id' => $this->vendor->id,
            'warehouse_id' => $this->warehouse->id,
            'received_by_id' => $this->user->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/grns/{$grn->id}", [
                'notes' => 'Updated notes',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot update GRN in current status',
            ]);
    }
}
