<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'name_en' => fake()->words(2, true),
            'default_useful_life' => fake()->numberBetween(12, 120),
            'default_depreciation_method' => fake()->randomElement(['straight_line', 'declining_balance']),
            'default_depreciation_rate' => fake()->randomFloat(2, 5, 25),
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }
}
