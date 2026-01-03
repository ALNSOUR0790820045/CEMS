<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('PRJ-####'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'status' => fake()->randomElement(['planning', 'active', 'on_hold', 'completed', 'cancelled']),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
