<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_name' => fake()->randomElement([
                'Al Rajhi Bank',
                'Saudi National Bank',
                'Riyad Bank',
                'Al Ahli Bank',
                'Arab National Bank',
                'SABB',
            ]),
            'account_number' => fake()->numerify('####################'),
            'iban' => 'SA' . fake()->numerify('######################'),
            'swift_code' => fake()->lexify('????????'),
            'is_primary' => fake()->boolean(30),
            'company_id' => Company::factory(),
        ];
    }
}
