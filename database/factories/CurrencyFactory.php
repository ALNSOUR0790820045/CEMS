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
            'code' => strtoupper(fake()->unique()->currencyCode()),
            'name' => fake()->currencyCode() . ' Currency',
            'name_en' => fake()->currencyCode() . ' Currency',
            'symbol' => fake()->randomElement(['$', '€', '£', 'د.ا', 'ر.س']),
            'exchange_rate' => fake()->randomFloat(4, 0.5, 5),
            'is_base' => false,
            'is_active' => true,
        ];
    }

    public function base()
    {
        return $this->state(function (array $attributes) {
            return [
                'code' => 'JOD',
                'name' => 'Jordanian Dinar',
                'name_en' => 'Jordanian Dinar',
                'symbol' => 'د.ا',
                'exchange_rate' => 1.0000,
                'is_base' => true,
            ];
        });
    }
}
