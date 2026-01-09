<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('WH[0-9]{3}'),
            'name' => fake()->words(3, true) . ' Warehouse',
            'warehouse_code' => fake()->unique()->numerify('WH-###'),
            'warehouse_name' => fake()->words(3, true),
            'warehouse_type' => fake()->randomElement(['main', 'site', 'temporary', 'transit']),
            'location' => fake()->city(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'manager_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}