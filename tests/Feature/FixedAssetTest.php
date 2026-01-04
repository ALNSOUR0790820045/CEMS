<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use App\Models\AssetDisposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FixedAssetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a test company
        $this->company = Company::factory()->create();
    }

    public function test_can_create_fixed_asset(): void
    {
        $response = $this->postJson('/api/fixed-assets', [
            'asset_name' => 'Test Computer',
            'asset_category' => 'computer',
            'purchase_date' => '2024-01-01',
            'purchase_cost' => 1000.00,
            'depreciation_method' => 'straight_line',
            'useful_life_years' => 5,
            'company_id' => $this->company->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'asset_code',
                'asset_name',
                'book_value',
            ]);
    }

    public function test_can_list_fixed_assets(): void
    {
        FixedAsset::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/fixed-assets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'asset_code',
                        'asset_name',
                    ]
                ]
            ]);
    }

    public function test_can_calculate_depreciation(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'purchase_cost' => 12000,
            'salvage_value' => 0,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
        ]);

        $response = $this->postJson('/api/fixed-assets/calculate-depreciation', [
            'period_date' => '2024-01-31',
            'asset_ids' => [$asset->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'results' => [
                    '*' => [
                        'asset_id',
                        'asset_code',
                        'status',
                        'depreciation_amount',
                    ]
                ]
            ]);
    }

    public function test_can_dispose_asset(): void
    {
        $asset = FixedAsset::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/fixed-assets/{$asset->id}/dispose", [
            'disposal_date' => '2024-12-31',
            'disposal_type' => 'sale',
            'disposal_amount' => 500,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'disposal' => [
                    'id',
                    'disposal_type',
                    'gain_loss',
                ]
            ]);

        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'status' => 'disposed',
        ]);
    }

    public function test_cannot_dispose_already_disposed_asset(): void
    {
        $asset = FixedAsset::factory()->create(['company_id' => $this->company->id]);
        AssetDisposal::factory()->create(['fixed_asset_id' => $asset->id, 'company_id' => $this->company->id]);

        $response = $this->postJson("/api/fixed-assets/{$asset->id}/dispose", [
            'disposal_date' => '2024-12-31',
            'disposal_type' => 'sale',
        ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Asset already disposed']);
    }

    public function test_asset_code_is_auto_generated(): void
    {
        $asset1 = FixedAsset::factory()->create(['company_id' => $this->company->id]);
        $asset2 = FixedAsset::factory()->create(['company_id' => $this->company->id]);

        $this->assertStringContainsString('FA-', $asset1->asset_code);
        $this->assertStringContainsString('FA-', $asset2->asset_code);
        $this->assertNotEquals($asset1->asset_code, $asset2->asset_code);
    }

    public function test_book_value_is_computed_correctly(): void
    {
        $asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'purchase_cost' => 10000,
            'accumulated_depreciation' => 3000,
        ]);

        $this->assertEquals(7000, $asset->book_value);
    }
}
