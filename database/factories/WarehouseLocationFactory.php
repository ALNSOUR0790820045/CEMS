<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseLocation>
 */
class WarehouseLocationFactory extends Factory
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
            'location_code' => fake()->unique()->bothify('LOC-??-##'),
            'location_name' => fake()->words(2, true),
            'location_type' => fake()->randomElement(['zone', 'rack', 'bin', 'shelf']),
            'capacity' => fake()->randomFloat(2, 100, 10000),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
