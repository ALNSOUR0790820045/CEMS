<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'code' => strtoupper(fake()->unique()->bothify('EXP-###')),
            'name' => fake()->words(3, true),
            'name_en' => fake()->words(3, true),
            'gl_account_id' => null,
            'spending_limit' => fake()->randomFloat(2, 100, 5000),
            'requires_receipt' => fake()->boolean(70),
            'is_active' => true,
        ];
    }
}
