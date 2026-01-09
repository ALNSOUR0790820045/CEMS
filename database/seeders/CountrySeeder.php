<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'السعودية', 'name_en' => 'Saudi Arabia', 'code' => 'SA', 'phone_code' => '+966'],
            ['name' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'code' => 'AE', 'phone_code' => '+971'],
            ['name' => 'الكويت', 'name_en' => 'Kuwait', 'code' => 'KW', 'phone_code' => '+965'],
            ['name' => 'قطر', 'name_en' => 'Qatar', 'code' => 'QA', 'phone_code' => '+974'],
            ['name' => 'البحرين', 'name_en' => 'Bahrain', 'code' => 'BH', 'phone_code' => '+973'],
            ['name' => 'عمان', 'name_en' => 'Oman', 'code' => 'OM', 'phone_code' => '+968'],
            ['name' => 'مصر', 'name_en' => 'Egypt', 'code' => 'EG', 'phone_code' => '+20'],
            ['name' => 'الأردن', 'name_en' => 'Jordan', 'code' => 'JO', 'phone_code' => '+962'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
