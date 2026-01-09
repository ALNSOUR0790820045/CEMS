<?php

namespace Database\Factories;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class FixedAssetFactory extends Factory
{
    protected $model = FixedAsset::class;

    public function definition(): array
    {
        $acquisitionCost = fake()->numberBetween(1000, 50000);
        $salvageValue = $acquisitionCost * 0.1;
        
        return [
            'asset_name' => fake()->words(3, true),
            'asset_name_en' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category_id' => AssetCategory::factory(),
            'serial_number' => fake()->unique()->bothify('SN-####-####'),
            'acquisition_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'acquisition_cost' => $acquisitionCost,
            'currency_id' => Currency::factory(),
            'useful_life_years' => fake()->numberBetween(3, 10),
            'useful_life_months' => 0,
            'salvage_value' => $salvageValue,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'net_book_value' => $acquisitionCost,
            'status' => 'active',
            'company_id' => Company::factory(),
        ];
    }
}
