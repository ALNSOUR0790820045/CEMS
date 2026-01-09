<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_name' => fake()->words(3, true) . ' Account',
            'account_number' => fake()->numerify('####################'),
            'bank_name' => fake()->randomElement([
                'Al Rajhi Bank',
                'Saudi National Bank',
                'Riyad Bank',
                'Al Ahli Bank',
                'Arab National Bank',
                'SABB',
            ]),
            'branch' => fake()->city() . ' Branch',
            'iban' => 'SA' . fake()->numerify('######################'),
            'swift_code' => fake()->lexify('????????'),
            'currency_id' => Currency::factory(),
            'balance' => fake()->randomFloat(2, 1000, 100000),
            'current_balance' => fake()->randomFloat(2, 1000, 100000),
            'bank_balance' => fake()->randomFloat(2, 1000, 100000),
            'is_active' => true,
            'is_primary' => fake()->boolean(30),
            'company_id' => Company::factory(),
        ];
    }
}
