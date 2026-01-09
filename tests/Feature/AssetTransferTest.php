<?php

namespace Tests\Feature;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\AssetTransfer;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Models\Department;
use App\Models\WarehouseLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssetTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected AssetCategory $category;
    protected Currency $currency;
    protected FixedAsset $asset;
    protected Department $fromDept;
    protected Department $toDept;
    protected WarehouseLocation $fromLocation;
    protected WarehouseLocation $toLocation;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create(['code' => 'USD']);
        $this->category = AssetCategory::factory()->create(['company_id' => $this->company->id]);
        
        $this->fromDept = Department::factory()->create(['company_id' => $this->company->id]);
        $this->toDept = Department::factory()->create(['company_id' => $this->company->id]);
        $this->fromLocation = WarehouseLocation::factory()->create(['company_id' => $this->company->id]);
        $this->toLocation = WarehouseLocation::factory()->create(['company_id' => $this->company->id]);
        
        $this->asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'department_id' => $this->fromDept->id,
            'location_id' => $this->fromLocation->id,
            'status' => 'active',
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_asset_transfer(): void
    {
        $transferData = [
            'fixed_asset_id' => $this->asset->id,
            'transfer_date' => now()->format('Y-m-d'),
            'to_department_id' => $this->toDept->id,
            'to_location_id' => $this->toLocation->id,
            'reason' => 'Relocation to new office',
        ];

        $response = $this->postJson('/api/asset-transfers', $transferData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'transfer_number',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('asset_transfers', [
            'fixed_asset_id' => $this->asset->id,
            'status' => 'pending',
        ]);
    }

    public function test_transfer_number_is_auto_generated(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
        ]);

        $this->assertNotNull($transfer->transfer_number);
        $this->assertStringStartsWith('ATR-' . date('Y') . '-', $transfer->transfer_number);
    }

    public function test_can_approve_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/asset-transfers/{$transfer->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('asset_transfers', [
            'id' => $transfer->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_can_complete_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'to_department_id' => $this->toDept->id,
            'to_location_id' => $this->toLocation->id,
            'status' => 'approved',
        ]);

        $response = $this->postJson("/api/asset-transfers/{$transfer->id}/complete");

        $response->assertStatus(200);

        $this->assertDatabaseHas('asset_transfers', [
            'id' => $transfer->id,
            'status' => 'completed',
        ]);

        // Check asset was updated
        $asset = $this->asset->fresh();
        $this->assertEquals($this->toDept->id, $asset->department_id);
        $this->assertEquals($this->toLocation->id, $asset->location_id);
    }

    public function test_cannot_complete_pending_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/asset-transfers/{$transfer->id}/complete");

        $response->assertStatus(422);
    }

    public function test_can_list_transfers(): void
    {
        AssetTransfer::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/asset-transfers');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'transfer_number',
                                 'status'
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_filter_transfers_by_status(): void
    {
        AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/asset-transfers?status=pending');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
    }

    public function test_can_update_pending_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $updateData = [
            'reason' => 'Updated reason',
        ];

        $response = $this->putJson("/api/asset-transfers/{$transfer->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('asset_transfers', [
            'id' => $transfer->id,
            'reason' => 'Updated reason',
        ]);
    }

    public function test_cannot_update_completed_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $updateData = [
            'reason' => 'Updated reason',
        ];

        $response = $this->putJson("/api/asset-transfers/{$transfer->id}", $updateData);

        $response->assertStatus(422);
    }

    public function test_can_delete_pending_transfer(): void
    {
        $transfer = AssetTransfer::factory()->create([
            'company_id' => $this->company->id,
            'fixed_asset_id' => $this->asset->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->deleteJson("/api/asset-transfers/{$transfer->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('asset_transfers', [
            'id' => $transfer->id,
        ]);
    }
}
