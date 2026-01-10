<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Quantity
            ['name' => 'قطعة', 'name_en' => 'Piece', 'code' => 'PCS', 'type' => Unit::TYPE_QUANTITY],
            ['name' => 'حبة', 'name_en' => 'Unit', 'code' => 'UNIT', 'type' => Unit::TYPE_QUANTITY],
            ['name' => 'صندوق', 'name_en' => 'Box', 'code' => 'BOX', 'type' => Unit::TYPE_QUANTITY],
            ['name' => 'كرتون', 'name_en' => 'Carton', 'code' => 'CARTON', 'type' => Unit::TYPE_QUANTITY],
            
            // Weight
            ['name' => 'كيلو', 'name_en' => 'Kilogram', 'code' => 'KG', 'type' => Unit::TYPE_WEIGHT],
            ['name' => 'جرام', 'name_en' => 'Gram', 'code' => 'G', 'type' => Unit::TYPE_WEIGHT],
            ['name' => 'طن', 'name_en' => 'Ton', 'code' => 'TON', 'type' => Unit::TYPE_WEIGHT],
            
            // Length
            ['name' => 'متر', 'name_en' => 'Meter', 'code' => 'M', 'type' => Unit::TYPE_LENGTH],
            ['name' => 'سنتيمتر', 'name_en' => 'Centimeter', 'code' => 'CM', 'type' => Unit::TYPE_LENGTH],
            
            // Volume
            ['name' => 'لتر', 'name_en' => 'Liter', 'code' => 'L', 'type' => Unit::TYPE_VOLUME],
            ['name' => 'مليلتر', 'name_en' => 'Milliliter', 'code' => 'ML', 'type' => Unit::TYPE_VOLUME],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
