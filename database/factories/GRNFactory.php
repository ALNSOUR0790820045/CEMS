<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GRN>
 */
class GRNFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grn_number' => 'GRN-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'grn_date' => fake()->date(),
            'vendor_id' => \App\Models\Vendor::factory(),
            'warehouse_id' => \App\Models\Warehouse::factory(),
            'status' => 'received',
            'total_value' => fake()->randomFloat(2, 100, 10000),
            'received_by_id' => \App\Models\User::factory(),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
