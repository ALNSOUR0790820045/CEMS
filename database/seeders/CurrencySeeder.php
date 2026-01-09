<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'ريال سعودي',
                'name_en' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => '﷼',
            ],
            [
                'name' => 'درهم إماراتي',
                'name_en' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
            ],
            [
                'name' => 'دينار كويتي',
                'name_en' => 'Kuwaiti Dinar',
                'code' => 'KWD',
                'symbol' => 'د.ك',
            ],
            [
                'name' => 'ريال قطري',
                'name_en' => 'Qatari Riyal',
                'code' => 'QAR',
                'symbol' => '﷼',
            ],
            [
                'name' => 'دينار بحريني',
                'name_en' => 'Bahraini Dinar',
                'code' => 'BHD',
                'symbol' => 'د.ب',
            ],
            [
                'name' => 'ريال عماني',
                'name_en' => 'Omani Rial',
                'code' => 'OMR',
                'symbol' => '﷼',
            ],
            [
                'name' => 'دولار أمريكي',
                'name_en' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
            ],
            [
                'name' => 'يورو',
                'name_en' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
