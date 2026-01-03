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
            'name' => fake()->words(3, true) . ' Warehouse',
            'code' => fake()->unique()->regexify('WH[0-9]{3}'),
            'location' => fake()->city(),
            'address' => fake()->address(),
            'manager_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
