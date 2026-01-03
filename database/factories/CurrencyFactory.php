<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->currencyCode(),
            'name' => fake()->words(2, true),
            'symbol' => fake()->randomElement(['$', '€', '£', '¥', 'SR']),
            'is_active' => true,
        ];
    }
}
