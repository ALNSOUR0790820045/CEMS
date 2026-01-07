<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->currencyCode(),
            'name' => fake()->word(),
            'symbol' => fake()->randomElement(['$', '€', '£', '¥']),
            'exchange_rate' => fake()->randomFloat(4, 0.5, 1.5),
            'is_active' => true,
        ];
    }
}
