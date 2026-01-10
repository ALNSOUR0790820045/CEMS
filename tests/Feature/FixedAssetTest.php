<?php

namespace Tests\Feature;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FixedAssetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected AssetCategory $category;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create(['code' => 'USD']);
        $this->category = AssetCategory::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_fixed_asset(): void
    {
        $assetData = [
            'asset_name' => 'Office Computer',
            'asset_name_en' => 'Office Computer',
            'description' => 'Dell Desktop Computer',
            'category_id' => $this->category->id,
            'acquisition_date' => '2024-01-01',
            'acquisition_cost' => 1500.00,
            'currency_id' => $this->currency->id,
            'useful_life_years' => 5,
            'useful_life_months' => 0,
            'salvage_value' => 100.00,
            'depreciation_method' => 'straight_line',
        ];

        $response = $this->postJson('/api/fixed-assets', $assetData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'asset_code',
                         'asset_name',
                         'acquisition_cost',
                         'net_book_value'
                     ]
                 ]);

        $this->assertDatabaseHas('fixed_assets', [
            'asset_name' => 'Office Computer',
            'acquisition_cost' => 1500.00,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_asset_code_is_auto_generated(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);

        $this->assertNotNull($asset->asset_code);
        $this->assertStringStartsWith('FA-' . date('Y') . '-', $asset->asset_code);
    }

    public function test_can_list_fixed_assets(): void
    {
        FixedAsset::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/fixed-assets');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'asset_code',
                                 'asset_name',
                                 'status'
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_filter_assets_by_category(): void
    {
        $category2 = AssetCategory::factory()->create(['company_id' => $this->company->id]);
        
        FixedAsset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);
        
        FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $category2->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/fixed-assets?category_id=' . $this->category->id);

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    public function test_can_view_single_asset(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/fixed-assets/{$asset->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'asset_code',
                         'asset_name',
                         'category',
                         'currency'
                     ]
                 ]);
    }

    public function test_can_update_fixed_asset(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);

        $updateData = [
            'asset_name' => 'Updated Asset Name',
            'status' => 'under_maintenance',
        ];

        $response = $this->putJson("/api/fixed-assets/{$asset->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'asset_name' => 'Updated Asset Name',
            'status' => 'under_maintenance',
        ]);
    }

    public function test_can_delete_fixed_asset(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->deleteJson("/api/fixed-assets/{$asset->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('fixed_assets', [
            'id' => $asset->id,
        ]);
    }

    public function test_net_book_value_is_calculated_correctly(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'acquisition_cost' => 10000,
            'accumulated_depreciation' => 2000,
        ]);

        $this->assertEquals(8000, $asset->fresh()->net_book_value);
    }

    public function test_can_search_assets(): void
    {
        FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'asset_name' => 'Laptop Computer',
        ]);

        FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'asset_name' => 'Office Desk',
        ]);

        $response = $this->getJson('/api/fixed-assets?search=Laptop');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals('Laptop Computer', $data[0]['asset_name']);
    }
}
