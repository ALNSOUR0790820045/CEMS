<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'budget_name' => fake()->words(3, true),
            'fiscal_year' => fake()->numberBetween(2020, 2030),
            'budget_type' => fake()->randomElement(['operating', 'capital', 'project']),
            'status' => fake()->randomElement(['draft', 'approved', 'active', 'closed']),
            'total_budget' => fake()->randomFloat(2, 10000, 1000000),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
