<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PettyCashAccount>
 */
class PettyCashAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $floatAmount = fake()->randomFloat(2, 500, 10000);
        return [
            'company_id' => \App\Models\Company::factory(),
            'account_code' => strtoupper(fake()->unique()->bothify('PCA-###')),
            'account_name' => fake()->words(3, true) . ' Petty Cash',
            'custodian_id' => \App\Models\User::factory(),
            'float_amount' => $floatAmount,
            'current_balance' => $floatAmount,
            'minimum_balance' => $floatAmount * 0.2,
            'gl_account_id' => null,
            'project_id' => null,
            'branch_id' => null,
            'is_active' => true,
        ];
    }
}
