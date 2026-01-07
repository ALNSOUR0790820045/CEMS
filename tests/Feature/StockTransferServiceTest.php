<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Material;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;
    protected Material $material;
    protected Warehouse $fromWarehouse;
    protected Warehouse $toWarehouse;
    protected StockTransferService $transferService;
    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = new InventoryService();
        $this->transferService = new StockTransferService($this->inventoryService);

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'name_en' => 'Test Company',
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

        $this->material = Material::create([
            'code' => 'MAT001',
            'name' => 'Test Material',
            'unit' => 'PCS',
            'standard_cost' => 10.00,
            'company_id' => $this->company->id,
        ]);

        $this->fromWarehouse = Warehouse::create([
            'code' => 'WH001',
            'name' => 'Main Warehouse',
            'company_id' => $this->company->id,
        ]);

        $this->toWarehouse = Warehouse::create([
            'code' => 'WH002',
            'name' => 'Secondary Warehouse',
            'company_id' => $this->company->id,
        ]);

        // Add initial stock to from warehouse
        $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->fromWarehouse->id,
            'quantity' => 200,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);
    }

    public function test_can_create_stock_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $this->assertNotNull($transfer);
        $this->assertEquals('pending', $transfer->status);
        $this->assertCount(1, $transfer->items);
        $this->assertTrue(str_starts_with($transfer->transfer_number, 'STR-'));
    }

    public function test_can_approve_stock_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $approvedTransfer = $this->transferService->approve($transfer->id, $this->user->id);

        $this->assertEquals('approved', $approvedTransfer->status);
        $this->assertEquals($this->user->id, $approvedTransfer->approved_by_id);
        $this->assertEquals(50, $approvedTransfer->items->first()->transferred_quantity);
    }

    public function test_cannot_approve_non_pending_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $this->transferService->approve($transfer->id, $this->user->id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only pending transfers can be approved');

        $this->transferService->approve($transfer->id, $this->user->id);
    }

    public function test_can_receive_stock_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $this->transferService->approve($transfer->id, $this->user->id);
        $receivedTransfer = $this->transferService->receive($transfer->id, $this->user->id, []);

        $this->assertEquals('completed', $receivedTransfer->status);
        $this->assertEquals($this->user->id, $receivedTransfer->received_by_id);

        // Check inventory balances
        $fromBalance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->fromWarehouse->id
        );
        $toBalance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->toWarehouse->id
        );

        $this->assertEquals(150, $fromBalance->quantity_on_hand); // 200 - 50
        $this->assertEquals(50, $toBalance->quantity_on_hand); // 0 + 50
    }

    public function test_can_cancel_stock_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $cancelledTransfer = $this->transferService->cancel($transfer->id, 'Test cancellation');

        $this->assertEquals('cancelled', $cancelledTransfer->status);
        $this->assertStringContainsString('Test cancellation', $cancelledTransfer->notes);
    }

    public function test_cannot_cancel_completed_transfer(): void
    {
        $transfer = $this->transferService->create([
            'transfer_date' => now()->format('Y-m-d'),
            'from_warehouse_id' => $this->fromWarehouse->id,
            'to_warehouse_id' => $this->toWarehouse->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'items' => [
                [
                    'material_id' => $this->material->id,
                    'requested_quantity' => 50,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $this->transferService->approve($transfer->id, $this->user->id);
        $this->transferService->receive($transfer->id, $this->user->id, []);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Completed transfers cannot be cancelled');

        $this->transferService->cancel($transfer->id);
    }
}
