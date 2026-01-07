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
        // Get Saudi Arabia country
        $saudiArabia = \App\Models\Country::where('code', 'SA')->first();
        
        if ($saudiArabia) {
            $saudiCities = [
                ['country_id' => $saudiArabia->id, 'name' => 'الرياض', 'name_en' => 'Riyadh', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'جدة', 'name_en' => 'Jeddah', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'مكة المكرمة', 'name_en' => 'Makkah', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'المدينة المنورة', 'name_en' => 'Madinah', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'الدمام', 'name_en' => 'Dammam', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'الخبر', 'name_en' => 'Khobar', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'الطائف', 'name_en' => 'Taif', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'أبها', 'name_en' => 'Abha', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'تبوك', 'name_en' => 'Tabuk', 'is_active' => true],
                ['country_id' => $saudiArabia->id, 'name' => 'القصيم', 'name_en' => 'Qassim', 'is_active' => true],
            ];
            
            foreach ($saudiCities as $city) {
                \App\Models\City:: create($city);
            }
        }

        // Get UAE country
        $uae = \App\Models\Country::where('code', 'AE')->first();
        
        if ($uae) {
            $uaeCities = [
                ['country_id' => $uae->id, 'name' => 'دبي', 'name_en' => 'Dubai', 'is_active' => true],
                ['country_id' => $uae->id, 'name' => 'أبو ظبي', 'name_en' => 'Abu Dhabi', 'is_active' => true],
                ['country_id' => $uae->id, 'name' => 'الشارقة', 'name_en' => 'Sharjah', 'is_active' => true],
                ['country_id' => $uae->id, 'name' => 'عجمان', 'name_en' => 'Ajman', 'is_active' => true],
            ];
            
            foreach ($uaeCities as $city) {
                \App\Models\City::create($city);
            }
        }
    }
}