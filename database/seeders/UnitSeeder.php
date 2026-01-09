<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Length
            ['code' => 'm', 'name' => 'متر', 'name_en' => 'Meter', 'category' => 'length'],
            ['code' => 'cm', 'name' => 'سنتيمتر', 'name_en' => 'Centimeter', 'category' => 'length'],
            ['code' => 'km', 'name' => 'كيلومتر', 'name_en' => 'Kilometer', 'category' => 'length'],
            
            // Area
            ['code' => 'm2', 'name' => 'متر مربع', 'name_en' => 'Square Meter', 'category' => 'area'],
            ['code' => 'km2', 'name' => 'كيلومتر مربع', 'name_en' => 'Square Kilometer', 'category' => 'area'],
            
            // Volume
            ['code' => 'm3', 'name' => 'متر مكعب', 'name_en' => 'Cubic Meter', 'category' => 'volume'],
            ['code' => 'l', 'name' => 'لتر', 'name_en' => 'Liter', 'category' => 'volume'],
            
            // Weight
            ['code' => 'kg', 'name' => 'كيلوجرام', 'name_en' => 'Kilogram', 'category' => 'weight'],
            ['code' => 'ton', 'name' => 'طن', 'name_en' => 'Ton', 'category' => 'weight'],
            
            // Count
            ['code' => 'no', 'name' => 'عدد', 'name_en' => 'Number', 'category' => 'count'],
            ['code' => 'pcs', 'name' => 'قطعة', 'name_en' => 'Piece', 'category' => 'count'],
            
            // Time
            ['code' => 'hr', 'name' => 'ساعة', 'name_en' => 'Hour', 'category' => 'time'],
            ['code' => 'day', 'name' => 'يوم', 'name_en' => 'Day', 'category' => 'time'],
            ['code' => 'month', 'name' => 'شهر', 'name_en' => 'Month', 'category' => 'time'],
            
            // Other
            ['code' => 'ls', 'name' => 'مجموع', 'name_en' => 'Lump Sum', 'category' => 'other'],
            ['code' => 'lot', 'name' => 'دفعة', 'name_en' => 'Lot', 'category' => 'other'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
