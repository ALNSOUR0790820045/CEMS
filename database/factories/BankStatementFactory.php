<?php

namespace Database\Factories;

use App\Models\BankStatement;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankStatement>
 */
class BankStatementFactory extends Factory
{
    protected $model = BankStatement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openingBalance = fake()->randomFloat(2, 1000, 50000);
        $deposits = fake()->randomFloat(2, 500, 10000);
        $withdrawals = fake()->randomFloat(2, 500, 8000);
        $closingBalance = $openingBalance + $deposits - $withdrawals;

        return [
            'bank_account_id' => BankAccount::factory(),
            'statement_date' => fake()->date(),
            'period_from' => fake()->date(),
            'period_to' => fake()->date(),
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'total_deposits' => $deposits,
            'total_withdrawals' => $withdrawals,
            'status' => fake()->randomElement(['imported', 'reconciling', 'reconciled']),
            'company_id' => Company::factory(),
        ];
    }
}
