<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Material;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;
    protected Material $material;
    protected Warehouse $warehouse;
    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = new InventoryService();

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

        $this->warehouse = Warehouse::create([
            'code' => 'WH001',
            'name' => 'Main Warehouse',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_record_receipt(): void
    {
        $transaction = $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $this->assertNotNull($transaction);
        $this->assertEquals('receipt', $transaction->transaction_type);
        $this->assertEquals(100, $transaction->quantity);

        // Check inventory balance
        $balance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->warehouse->id
        );

        $this->assertEquals(100, $balance->quantity_on_hand);
        $this->assertEquals(10.00, $balance->average_cost);
    }

    public function test_can_record_issue(): void
    {
        // First record a receipt
        $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        // Then record an issue
        $transaction = $this->inventoryService->recordIssue([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 30,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $this->assertNotNull($transaction);
        $this->assertEquals('issue', $transaction->transaction_type);
        $this->assertEquals(-30, $transaction->quantity);

        // Check inventory balance
        $balance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->warehouse->id
        );

        $this->assertEquals(70, $balance->quantity_on_hand);
    }

    public function test_cannot_issue_more_than_available(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock available');

        $this->inventoryService->recordIssue([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);
    }

    public function test_can_record_adjustment(): void
    {
        $transaction = $this->inventoryService->recordAdjustment([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 50,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $this->assertNotNull($transaction);
        $this->assertEquals('adjustment', $transaction->transaction_type);

        $balance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->warehouse->id
        );

        $this->assertEquals(50, $balance->quantity_on_hand);
    }

    public function test_average_cost_calculation(): void
    {
        // First receipt: 100 units @ 10.00
        $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        // Second receipt: 50 units @ 12.00
        $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 50,
            'unit_cost' => 12.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $balance = $this->inventoryService->getBalance(
            $this->material->id,
            $this->warehouse->id
        );

        // Average cost = (100 * 10 + 50 * 12) / 150 = 1600 / 150 = 10.67
        $this->assertEquals(150, $balance->quantity_on_hand);
        $this->assertEquals(10.67, round($balance->average_cost, 2));
    }

    public function test_can_get_valuation_report(): void
    {
        $this->inventoryService->recordReceipt([
            'transaction_date' => now()->format('Y-m-d'),
            'material_id' => $this->material->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100,
            'unit_cost' => 10.00,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $report = $this->inventoryService->getValuationReport($this->company->id);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('items', $report);
        $this->assertArrayHasKey('total_value', $report);
        $this->assertArrayHasKey('total_quantity', $report);
        $this->assertEquals(1000, $report['total_value']);
        $this->assertEquals(100, $report['total_quantity']);
    }
}
