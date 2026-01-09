<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('MAT[0-9]{3}'),
            'name' => fake()->words(3, true),
            'material_code' => fake()->unique()->numerify('MAT-####'),
            'material_name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit' => fake()->randomElement(['piece', 'kg', 'liter', 'meter']),
            'unit_of_measure' => fake()->randomElement(['pcs', 'kg', 'liter', 'meter', 'box']),
            'unit_price' => fake()->randomFloat(2, 10, 1000),
            'category' => fake()->randomElement(['Raw Material', 'Finished Goods', 'Supplies']),
            'reorder_level' => fake()->randomFloat(2, 10, 100),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}