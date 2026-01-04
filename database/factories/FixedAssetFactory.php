<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FixedAsset>
 */
class FixedAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_code' => 'FA-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'asset_name' => fake()->words(3, true),
            'asset_category' => fake()->randomElement(['building', 'equipment', 'vehicle', 'furniture', 'computer', 'other']),
            'asset_type' => fake()->words(2, true),
            'purchase_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'purchase_cost' => fake()->randomFloat(2, 1000, 50000),
            'supplier_id' => null,
            'serial_number' => fake()->bothify('SN-####-????'),
            'location' => fake()->city(),
            'department_id' => null,
            'custodian_id' => null,
            'depreciation_method' => fake()->randomElement(['straight_line', 'declining_balance', 'units_of_production']),
            'useful_life_years' => fake()->numberBetween(3, 10),
            'salvage_value' => fake()->randomFloat(2, 0, 1000),
            'accumulated_depreciation' => 0,
            'status' => 'active',
            'gl_asset_account_id' => null,
            'gl_depreciation_account_id' => null,
            'gl_accumulated_depreciation_account_id' => null,
            'warranty_expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
            'notes' => fake()->sentence(),
        ];
    }
}
