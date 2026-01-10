<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseStock>
 */
class WarehouseStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => \App\Models\Warehouse::factory(),
            'material_id' => \App\Models\Material::factory(),
            'location_id' => null,
            'quantity' => fake()->randomFloat(2, 10, 1000),
            'reserved_quantity' => fake()->randomFloat(2, 0, 50),
            'batch_number' => fake()->optional()->numerify('BATCH-####'),
            'expiry_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
            'last_updated' => now(),
        ];
    }
}
