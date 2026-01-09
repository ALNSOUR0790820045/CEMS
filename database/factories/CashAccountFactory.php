<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashAccount>
 */
class CashAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_name' => fake()->randomElement(['Main Cash', 'Petty Cash', 'Bank Account']) . ' - ' . fake()->city(),
            'account_name_en' => fake()->randomElement(['Main Cash', 'Petty Cash', 'Bank Account']) . ' - ' . fake()->city(),
            'account_type' => fake()->randomElement(['cash', 'bank', 'petty_cash', 'safe']),
            'opening_balance' => fake()->randomFloat(2, 0, 100000),
            'current_balance' => fake()->randomFloat(2, 0, 100000),
            'is_active' => true,
        ];
    }
}
