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
                'code' => 'SA',
                'name' => 'السعودية',
                'name_en' => 'Saudi Arabia',
                'is_active' => true,
            ],
            [
                'code' => 'AE',
                'name' => 'الإمارات',
                'name_en' => 'United Arab Emirates',
                'is_active' => true,
            ],
            [
                'code' => 'KW',
                'name' => 'الكويت',
                'name_en' => 'Kuwait',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }
    }
}
