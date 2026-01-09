<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->currencyCode(),
            'name' => fake()->words(2, true),
            'name_en' => fake()->words(2, true),
            'symbol' => fake()->randomElement(['$', '€', '£', 'SAR', 'AED']),
            'exchange_rate' => fake()->randomFloat(4, 0.5, 5),
            'is_base' => false,
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }

    public function base(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_base' => true,
            'exchange_rate' => 1.0000,
        ]);
    }
}
