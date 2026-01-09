<?php

namespace Database\Factories;

use App\Models\BankReconciliation;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankReconciliation>
 */
class BankReconciliationFactory extends Factory
{
    protected $model = BankReconciliation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookBalance = fake()->randomFloat(2, 5000, 50000);
        $bankBalance = fake()->randomFloat(2, 5000, 50000);
        $difference = $bookBalance - $bankBalance;

        return [
            'bank_account_id' => BankAccount::factory(),
            'reconciliation_date' => fake()->date(),
            'period_from' => fake()->date(),
            'period_to' => fake()->date(),
            'book_balance' => $bookBalance,
            'bank_balance' => $bankBalance,
            'adjusted_book_balance' => $bookBalance,
            'adjusted_bank_balance' => $bankBalance,
            'difference' => $difference,
            'status' => fake()->randomElement(['draft', 'in_progress', 'completed', 'approved']),
            'prepared_by_id' => User::factory(),
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
