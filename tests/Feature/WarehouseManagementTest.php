<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Material;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_can_create_warehouse(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/warehouses', [
                'warehouse_code' => 'WH001',
                'warehouse_name' => 'Main Warehouse',
                'warehouse_type' => 'main',
                'address' => '123 Main St',
                'city' => 'Amman',
                'country' => 'Jordan',
                'phone' => '+962791234567',
                'is_active' => true,
                'company_id' => $this->company->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Warehouse created successfully',
            ]);
    }

    public function test_can_list_warehouses(): void
    {
        Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/warehouses');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_can_create_warehouse_location(): void
    {
        $warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/warehouse-locations', [
                'warehouse_id' => $warehouse->id,
                'location_code' => 'A-01',
                'location_name' => 'Zone A',
                'location_type' => 'zone',
                'capacity' => 1000.00,
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Warehouse location created successfully',
            ]);
    }

    public function test_can_check_stock_availability(): void
    {
        $warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $material = Material::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        WarehouseStock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'material_id' => $material->id,
            'quantity' => 100,
            'reserved_quantity' => 20,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/warehouse-stock/availability?' . http_build_query([
                'material_id' => $material->id,
                'required_quantity' => 50,
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_available' => true,
                    'total_available' => 80,
                ],
            ]);
    }

    public function test_can_transfer_stock_between_warehouses(): void
    {
        $fromWarehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $toWarehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $material = Material::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        WarehouseStock::factory()->create([
            'warehouse_id' => $fromWarehouse->id,
            'material_id' => $material->id,
            'location_id' => null,
            'batch_number' => null,
            'quantity' => 100,
            'reserved_quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/warehouse-stock/transfer', [
                'material_id' => $material->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'quantity' => 30,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Stock transferred successfully',
            ]);
    }

    public function test_cannot_transfer_more_than_available_stock(): void
    {
        $fromWarehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $toWarehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $material = Material::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        WarehouseStock::factory()->create([
            'warehouse_id' => $fromWarehouse->id,
            'material_id' => $material->id,
            'location_id' => null,
            'batch_number' => null,
            'quantity' => 50,
            'reserved_quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/warehouse-stock/transfer', [
                'material_id' => $material->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'quantity' => 100,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient stock available for transfer',
            ]);
    }
}
