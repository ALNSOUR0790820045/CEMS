<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_code' => fake()->unique()->numerify('WH-###'),
            'warehouse_name' => fake()->words(3, true),
            'warehouse_type' => fake()->randomElement(['main', 'site', 'temporary', 'transit']),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
