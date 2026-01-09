<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'المملكة العربية السعودية',
                'name_en' => 'Saudi Arabia',
                'code' => 'SA',
                'code3' => 'SAU',
                'currency_code' => 'SAR',
                'phone_code' => '+966',
                'cities' => ['الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة']
            ],
            [
                'name' => 'الإمارات العربية المتحدة',
                'name_en' => 'United Arab Emirates',
                'code' => 'AE',
                'code3' => 'ARE',
                'currency_code' => 'AED',
                'phone_code' => '+971',
                'cities' => ['دبي', 'أبوظبي', 'الشارقة', 'عجمان', 'رأس الخيمة']
            ],
            [
                'name' => 'الكويت',
                'name_en' => 'Kuwait',
                'code' => 'KW',
                'code3' => 'KWT',
                'currency_code' => 'KWD',
                'phone_code' => '+965',
                'cities' => ['مدينة الكويت', 'الأحمدي', 'حولي', 'الفروانية', 'الجهراء']
            ],
            [
                'name' => 'قطر',
                'name_en' => 'Qatar',
                'code' => 'QA',
                'code3' => 'QAT',
                'currency_code' => 'QAR',
                'phone_code' => '+974',
                'cities' => ['الدوحة', 'الريان', 'الوكرة', 'الخور', 'مسيعيد']
            ],
            [
                'name' => 'البحرين',
                'name_en' => 'Bahrain',
                'code' => 'BH',
                'code3' => 'BHR',
                'currency_code' => 'BHD',
                'phone_code' => '+973',
                'cities' => ['المنامة', 'المحرق', 'الرفاع', 'مدينة حمد', 'مدينة عيسى']
            ],
            [
                'name' => 'عُمان',
                'name_en' => 'Oman',
                'code' => 'OM',
                'code3' => 'OMN',
                'currency_code' => 'OMR',
                'phone_code' => '+968',
                'cities' => ['مسقط', 'صلالة', 'صحار', 'نزوى', 'البريمي']
            ],
        ];

        foreach ($countries as $countryData) {
            $cities = $countryData['cities'];
            unset($countryData['cities']);
            
            $country = Country::create($countryData);

            foreach ($cities as $cityName) {
                City::create([
                    'country_id' => $country->id,
                    'name' => $cityName,
                    'name_en' => $cityName,
                    'is_active' => true,
                ]);
            }
        }
    }
}
