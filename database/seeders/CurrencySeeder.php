<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'SAR', 'name' => 'ريال سعودي', 'name_en' => 'Saudi Riyal', 'symbol' => 'ر.س', 'is_active' => true],
            ['code' => 'USD', 'name' => 'دولار أمريكي', 'name_en' => 'US Dollar', 'symbol' => '$', 'is_active' => true],
            ['code' => 'EUR', 'name' => 'يورو', 'name_en' => 'Euro', 'symbol' => '€', 'is_active' => true],
            ['code' => 'AED', 'name' => 'درهم إماراتي', 'name_en' => 'UAE Dirham', 'symbol' => 'د.إ', 'is_active' => true],
            ['code' => 'KWD', 'name' => 'دينار كويتي', 'name_en' => 'Kuwaiti Dinar', 'symbol' => 'د.ك', 'is_active' => true],
            ['code' => 'QAR', 'name' => 'ريال قطري', 'name_en' => 'Qatari Riyal', 'symbol' => 'ر.ق', 'is_active' => true],
            ['code' => 'BHD', 'name' => 'دينار بحريني', 'name_en' => 'Bahraini Dinar', 'symbol' => 'د.ب', 'is_active' => true],
            ['code' => 'OMR', 'name' => 'ريال عماني', 'name_en' => 'Omani Rial', 'symbol' => 'ر.ع', 'is_active' => true],
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::create($currency);
        }
    }
}
