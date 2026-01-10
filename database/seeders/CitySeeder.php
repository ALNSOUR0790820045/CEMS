<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Saudi Arabia cities
            ['country_code' => 'SA', 'name' => 'الرياض', 'name_en' => 'Riyadh', 'is_active' => true],
            ['country_code' => 'SA', 'name' => 'جدة', 'name_en' => 'Jeddah', 'is_active' => true],
            ['country_code' => 'SA', 'name' => 'الدمام', 'name_en' => 'Dammam', 'is_active' => true],
            ['country_code' => 'SA', 'name' => 'مكة', 'name_en' => 'Makkah', 'is_active' => true],
            ['country_code' => 'SA', 'name' => 'المدينة', 'name_en' => 'Madinah', 'is_active' => true],
            
            // UAE cities
            ['country_code' => 'AE', 'name' => 'دبي', 'name_en' => 'Dubai', 'is_active' => true],
            ['country_code' => 'AE', 'name' => 'أبوظبي', 'name_en' => 'Abu Dhabi', 'is_active' => true],
            ['country_code' => 'AE', 'name' => 'الشارقة', 'name_en' => 'Sharjah', 'is_active' => true],
            
            // Kuwait cities
            ['country_code' => 'KW', 'name' => 'الكويت', 'name_en' => 'Kuwait City', 'is_active' => true],
        ];

        foreach ($cities as $cityData) {
            $country = \App\Models\Country::where('code', $cityData['country_code'])->first();
            if ($country) {
                \App\Models\City::create([
                    'country_id' => $country->id,
                    'name' => $cityData['name'],
                    'name_en' => $cityData['name_en'],
                    'is_active' => $cityData['is_active'],
                ]);
            }
        }
    }
}
