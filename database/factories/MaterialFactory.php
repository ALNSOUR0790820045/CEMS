<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'code' => fake()->unique()->regexify('MAT[0-9]{3}'),
            'description' => fake()->sentence(),
            'unit' => fake()->randomElement(['piece', 'kg', 'liter', 'meter']),
            'unit_price' => fake()->randomFloat(2, 10, 1000),
            'category' => fake()->randomElement(['Raw Material', 'Finished Goods', 'Supplies']),
            'reorder_level' => fake()->randomFloat(2, 10, 100),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
