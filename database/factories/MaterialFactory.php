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
            'material_code' => fake()->unique()->numerify('MAT-####'),
            'material_name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit_of_measure' => fake()->randomElement(['pcs', 'kg', 'liter', 'meter', 'box']),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
