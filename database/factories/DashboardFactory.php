<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dashboard>
 */
class DashboardFactory extends Factory
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
            'name_en' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['executive', 'project', 'financial', 'hr', 'operations']),
            'layout' => null,
            'is_default' => false,
            'is_public' => fake()->boolean(),
            'created_by_id' => \App\Models\User::factory(),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
