<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PettyCashReplenishment>
 */
class PettyCashReplenishmentFactory extends Factory
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
            'replenishment_number' => 'PCR-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'replenishment_date' => fake()->date(),
            'petty_cash_account_id' => \App\Models\PettyCashAccount::factory(),
            'amount' => fake()->randomFloat(2, 500, 5000),
            'payment_method' => fake()->randomElement(['cash', 'check', 'transfer']),
            'reference_number' => fake()->optional()->bothify('REF-####'),
            'from_account_type' => fake()->randomElement(['cash', 'bank']),
            'from_account_id' => fake()->numberBetween(1, 10),
            'status' => 'pending',
            'requested_by_id' => \App\Models\User::factory(),
            'approved_by_id' => null,
            'approved_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
