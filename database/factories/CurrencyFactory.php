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
            'code' => $this->faker->unique()->currencyCode(),
            'name' => $this->faker->word(),
            'symbol' => $this->faker->randomElement(['$', '€', '£', '¥', 'SAR']),
            'exchange_rate' => $this->faker->randomFloat(4, 0.5, 5),
            'is_base' => false,
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
