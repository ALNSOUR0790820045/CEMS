<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'ريال سعودي', 'name_en' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => 'ر.س'],
            ['name' => 'درهم إماراتي', 'name_en' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ['name' => 'دينار كويتي', 'name_en' => 'Kuwaiti Dinar', 'code' => 'KWD', 'symbol' => 'د.ك'],
            ['name' => 'دولار أمريكي', 'name_en' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['name' => 'يورو', 'name_en' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
