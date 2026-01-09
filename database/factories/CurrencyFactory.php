<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س'],
            ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'symbol' => 'د.ا'],
        ];

        $currency = fake()->randomElement($currencies);

        return [
            'code' => $currency['code'],
            'name' => $currency['name'],
            'name_en' => $currency['name'],
            'symbol' => $currency['symbol'],
            'exchange_rate' => fake()->randomFloat(4, 0.5, 5),
            'is_base' => false,
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }
}
