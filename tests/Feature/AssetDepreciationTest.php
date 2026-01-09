<?php

namespace Tests\Feature;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\AssetDepreciation;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssetDepreciationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected AssetCategory $category;
    protected Currency $currency;
    protected FixedAsset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create(['code' => 'USD']);
        $this->category = AssetCategory::factory()->create(['company_id' => $this->company->id]);
        
        $this->asset = FixedAsset::factory()->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'acquisition_cost' => 12000,
            'salvage_value' => 2000,
            'useful_life_years' => 5,
            'useful_life_months' => 0,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_calculate_straight_line_depreciation(): void
    {
        // Monthly depreciation = (12000 - 2000) / 60 = 166.67
        $monthlyDepreciation = $this->asset->calculateMonthlyDepreciation();
        
        $this->assertEquals(166.67, round($monthlyDepreciation, 2));
    }

    public function test_can_calculate_monthly_depreciation_for_asset(): void
    {
        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $response = $this->postJson("/api/fixed-assets/{$this->asset->id}/calculate-depreciation", $depreciationData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'fixed_asset_id',
                         'depreciation_amount',
                         'accumulated_depreciation'
                     ]
                 ]);

        $this->assertDatabaseHas('asset_depreciations', [
            'fixed_asset_id' => $this->asset->id,
            'period_month' => 1,
            'period_year' => 2024,
        ]);
    }

    public function test_cannot_calculate_depreciation_twice_for_same_period(): void
    {
        // First calculation
        AssetDepreciation::factory()->create([
            'fixed_asset_id' => $this->asset->id,
            'period_month' => 1,
            'period_year' => 2024,
            'company_id' => $this->company->id,
        ]);

        // Try to calculate again
        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $response = $this->postJson("/api/fixed-assets/{$this->asset->id}/calculate-depreciation", $depreciationData);

        $response->assertStatus(422);
    }

    public function test_can_run_monthly_depreciation_for_all_assets(): void
    {
        // Create additional assets
        FixedAsset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
        ]);

        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $response = $this->postJson('/api/asset-depreciations/run-monthly', $depreciationData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'created',
                         'skipped'
                     ]
                 ]);

        $this->assertDatabaseCount('asset_depreciations', 3);
    }

    public function test_accumulated_depreciation_updates_asset(): void
    {
        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $this->postJson("/api/fixed-assets/{$this->asset->id}/calculate-depreciation", $depreciationData);

        $asset = $this->asset->fresh();
        
        $this->assertGreaterThan(0, $asset->accumulated_depreciation);
        $this->assertLessThan($asset->acquisition_cost, $asset->net_book_value);
    }

    public function test_can_list_depreciations(): void
    {
        AssetDepreciation::factory()->count(3)->create([
            'fixed_asset_id' => $this->asset->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/asset-depreciations');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'fixed_asset_id',
                                 'depreciation_amount',
                                 'is_posted'
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_preview_monthly_depreciation(): void
    {
        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $response = $this->getJson('/api/asset-depreciations/preview?' . http_build_query($depreciationData));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'period_month',
                         'period_year',
                         'assets',
                         'total_depreciation',
                         'asset_count'
                     ]
                 ]);
    }

    public function test_can_post_depreciations(): void
    {
        $depreciation1 = AssetDepreciation::factory()->create([
            'fixed_asset_id' => $this->asset->id,
            'company_id' => $this->company->id,
            'is_posted' => false,
        ]);

        $depreciation2 = AssetDepreciation::factory()->create([
            'fixed_asset_id' => $this->asset->id,
            'company_id' => $this->company->id,
            'is_posted' => false,
        ]);

        $response = $this->postJson('/api/asset-depreciations/post', [
            'depreciation_ids' => [$depreciation1->id, $depreciation2->id],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('asset_depreciations', [
            'id' => $depreciation1->id,
            'is_posted' => true,
        ]);

        $this->assertDatabaseHas('asset_depreciations', [
            'id' => $depreciation2->id,
            'is_posted' => true,
        ]);
    }

    public function test_depreciation_stops_at_salvage_value(): void
    {
        // Set asset close to salvage value
        $this->asset->update([
            'accumulated_depreciation' => 9900, // 12000 - 2100 = 2100 remaining
            'net_book_value' => 2100,
        ]);

        $depreciationData = [
            'period_month' => 1,
            'period_year' => 2024,
        ];

        $this->postJson("/api/fixed-assets/{$this->asset->id}/calculate-depreciation", $depreciationData);

        $asset = $this->asset->fresh();
        
        // Should depreciate only to salvage value
        $this->assertEquals(2000, $asset->net_book_value);
    }

    public function test_can_get_depreciation_schedule(): void
    {
        $response = $this->getJson("/api/fixed-assets/{$this->asset->id}/depreciation-schedule");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'asset',
                         'schedule' => [
                             '*' => [
                                 'period',
                                 'date',
                                 'depreciation_amount',
                                 'accumulated_depreciation',
                                 'net_book_value'
                             ]
                         ]
                     ]
                 ]);

        $schedule = $response->json('data.schedule');
        $this->assertCount(60, $schedule); // 5 years * 12 months
    }
}
