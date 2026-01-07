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
            ['code' => 'SA', 'name' => 'السعودية', 'name_en' => 'Saudi Arabia', 'phone_code' => '+966', 'is_active' => true],
            ['code' => 'AE', 'name' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'phone_code' => '+971', 'is_active' => true],
            ['code' => 'KW', 'name' => 'الكويت', 'name_en' => 'Kuwait', 'phone_code' => '+965', 'is_active' => true],
            ['code' => 'QA', 'name' => 'قطر', 'name_en' => 'Qatar', 'phone_code' => '+974', 'is_active' => true],
            ['code' => 'BH', 'name' => 'البحرين', 'name_en' => 'Bahrain', 'phone_code' => '+973', 'is_active' => true],
            ['code' => 'OM', 'name' => 'عمان', 'name_en' => 'Oman', 'phone_code' => '+968', 'is_active' => true],
            ['code' => 'EG', 'name' => 'مصر', 'name_en' => 'Egypt', 'phone_code' => '+20', 'is_active' => true],
            ['code' => 'JO', 'name' => 'الأردن', 'name_en' => 'Jordan', 'phone_code' => '+962', 'is_active' => true],
        ];

        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }
    }
}
