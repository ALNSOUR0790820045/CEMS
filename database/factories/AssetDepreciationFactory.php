<?php

namespace Database\Factories;

use App\Models\AssetDepreciation;
use App\Models\FixedAsset;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetDepreciationFactory extends Factory
{
    protected $model = AssetDepreciation::class;

    public function definition(): array
    {
        return [
            'fixed_asset_id' => FixedAsset::factory(),
            'depreciation_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'period_month' => fake()->numberBetween(1, 12),
            'period_year' => fake()->numberBetween(2023, 2024),
            'depreciation_amount' => fake()->randomFloat(2, 100, 1000),
            'accumulated_depreciation' => fake()->randomFloat(2, 1000, 10000),
            'net_book_value' => fake()->randomFloat(2, 5000, 20000),
            'is_posted' => false,
            'company_id' => Company::factory(),
        ];
    }
}
