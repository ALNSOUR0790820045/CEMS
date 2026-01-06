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
            ['name' => 'الرياض', 'name_en' => 'Riyadh'],
            ['name' => 'جدة', 'name_en' => 'Jeddah'],
            ['name' => 'مكة المكرمة', 'name_en' => 'Makkah'],
            ['name' => 'المدينة المنورة', 'name_en' => 'Madinah'],
            ['name' => 'الدمام', 'name_en' => 'Dammam'],
            ['name' => 'الخبر', 'name_en' => 'Khobar'],
            ['name' => 'الطائف', 'name_en' => 'Taif'],
            ['name' => 'أبها', 'name_en' => 'Abha'],
            ['name' => 'تبوك', 'name_en' => 'Tabuk'],
            ['name' => 'القصيم', 'name_en' => 'Qassim'],
        ];

        foreach ($cities as $city) {
            \App\Models\City::create($city);
        }
    }
}
