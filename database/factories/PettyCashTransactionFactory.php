<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PettyCashTransaction>
 */
class PettyCashTransactionFactory extends Factory
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
            'transaction_number' => 'PC-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'transaction_date' => fake()->date(),
            'petty_cash_account_id' => \App\Models\PettyCashAccount::factory(),
            'transaction_type' => fake()->randomElement(['expense', 'replenishment', 'adjustment']),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->sentence(),
            'expense_category_id' => null,
            'cost_center_id' => null,
            'project_id' => null,
            'receipt_number' => fake()->optional()->bothify('RCP-####'),
            'receipt_date' => fake()->optional()->date(),
            'payee_name' => fake()->optional()->name(),
            'status' => 'pending',
            'requested_by_id' => \App\Models\User::factory(),
            'approved_by_id' => null,
            'approved_at' => null,
            'posted_by_id' => null,
            'posted_at' => null,
            'gl_journal_entry_id' => null,
            'attachment_path' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
