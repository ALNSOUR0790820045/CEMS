<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'السعودية',
                'name_en' => 'Saudi Arabia',
                'code' => 'SA',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'is_active' => true,
            ],
            [
                'name' => 'الإمارات',
                'name_en' => 'UAE',
                'code' => 'AE',
                'phone_code' => '+971',
                'currency_code' => 'AED',
                'is_active' => true,
            ],
            [
                'name' => 'الكويت',
                'name_en' => 'Kuwait',
                'code' => 'KW',
                'phone_code' => '+965',
                'currency_code' => 'KWD',
                'is_active' => true,
            ],
            [
                'name' => 'قطر',
                'name_en' => 'Qatar',
                'code' => 'QA',
                'phone_code' => '+974',
                'currency_code' => 'QAR',
                'is_active' => true,
            ],
            [
                'name' => 'البحرين',
                'name_en' => 'Bahrain',
                'code' => 'BH',
                'phone_code' => '+973',
                'currency_code' => 'BHD',
                'is_active' => true,
            ],
            [
                'name' => 'عمان',
                'name_en' => 'Oman',
                'code' => 'OM',
                'phone_code' => '+968',
                'currency_code' => 'OMR',
                'is_active' => true,
            ],
            [
                'name' => 'الأردن',
                'name_en' => 'Jordan',
                'code' => 'JO',
                'phone_code' => '+962',
                'currency_code' => 'JOD',
                'is_active' => true,
            ],
            [
                'name' => 'مصر',
                'name_en' => 'Egypt',
                'code' => 'EG',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }
    }
}
