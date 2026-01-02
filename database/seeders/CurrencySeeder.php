<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'ريال سعودي',
                'name_en' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => 'ر.س',
                'exchange_rate' => 1.000000,
                'is_base' => true,
                'is_active' => true,
            ],
            [
                'name' => 'درهم إماراتي',
                'name_en' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'exchange_rate' => 1.020000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دينار كويتي',
                'name_en' => 'Kuwaiti Dinar',
                'code' => 'KWD',
                'symbol' => 'د.ك',
                'exchange_rate' => 0.300000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ريال قطري',
                'name_en' => 'Qatari Riyal',
                'code' => 'QAR',
                'symbol' => 'ر.ق',
                'exchange_rate' => 1.360000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دينار بحريني',
                'name_en' => 'Bahraini Dinar',
                'code' => 'BHD',
                'symbol' => 'د.ب',
                'exchange_rate' => 0.380000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ريال عماني',
                'name_en' => 'Omani Rial',
                'code' => 'OMR',
                'symbol' => 'ر.ع',
                'exchange_rate' => 0.380000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دينار أردني',
                'name_en' => 'Jordanian Dinar',
                'code' => 'JOD',
                'symbol' => 'د.أ',
                'exchange_rate' => 0.710000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'جنيه مصري',
                'name_en' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => 'ج.م',
                'exchange_rate' => 30.000000,
                'is_base' => false,
                'is_active' => true,
            ],
            [
                'name' => 'دولار أمريكي',
                'name_en' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 3.750000,
                'is_base' => false,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}

