<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'دينار أردني',
                'name_en' => 'Jordanian Dinar',
                'code' => 'JOD',
                'symbol' => 'د.أ',
                'symbol_position' => 'after',
                'decimal_places' => 3,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 0.710000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ريال سعودي',
                'name_en' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => 'ر.س',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 1.000000,
                'is_base' => true,
                'is_active' => true,
            ],
            [
                'name' => 'درهم إماراتي',
                'name_en' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 3.673000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دينار كويتي',
                'name_en' => 'Kuwaiti Dinar',
                'code' => 'KWD',
                'symbol' => 'د.ك',
                'symbol_position' => 'after',
                'decimal_places' => 3,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 0.307000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ريال قطري',
                'name_en' => 'Qatari Riyal',
                'code' => 'QAR',
                'symbol' => 'ر.ق',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 3.641000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دينار بحريني',
                'name_en' => 'Bahraini Dinar',
                'code' => 'BHD',
                'symbol' => 'د.ب',
                'symbol_position' => 'after',
                'decimal_places' => 3,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 0.376000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ريال عماني',
                'name_en' => 'Omani Rial',
                'code' => 'OMR',
                'symbol' => 'ر.ع',
                'symbol_position' => 'after',
                'decimal_places' => 3,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 0.385000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'جنيه مصري',
                'name_en' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => 'ج.م',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 49.000000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دولار أمريكي',
                'name_en' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 3.750000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'يورو',
                'name_en' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'thousands_separator' => ',',
                'decimal_separator' => '.',
                'exchange_rate' => 4.100000,
                'is_base' => false,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}