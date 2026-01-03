<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CostCenter>
 */
class CostCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('CC-###'),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['project', 'department', 'activity', 'asset']),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
