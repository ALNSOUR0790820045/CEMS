<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;
use App\Models\Equipment;
use App\Models\User;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create equipment categories
        $categories = [
            [
                'code' => 'EXC',
                'name' => 'حفارات',
                'name_en' => 'Excavators',
                'description' => 'معدات حفر ثقيلة',
                'is_active' => true,
            ],
            [
                'code' => 'BLD',
                'name' => 'بلدوزرات',
                'name_en' => 'Bulldozers',
                'description' => 'معدات تسوية وتحريك التربة',
                'is_active' => true,
            ],
            [
                'code' => 'TRK',
                'name' => 'شاحنات',
                'name_en' => 'Trucks',
                'description' => 'شاحنات نقل مختلفة',
                'is_active' => true,
            ],
            [
                'code' => 'CRN',
                'name' => 'رافعات',
                'name_en' => 'Cranes',
                'description' => 'رافعات بأنواعها',
                'is_active' => true,
            ],
            [
                'code' => 'GEN',
                'name' => 'مولدات كهربائية',
                'name_en' => 'Generators',
                'description' => 'مولدات الطاقة الكهربائية',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            EquipmentCategory::create($category);
        }

        // Create a test user if needed
        if (User::count() == 0) {
            User::create([
                'name' => 'مدير النظام',
                'email' => 'admin@cems.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create sample equipment
        $excavatorCategory = EquipmentCategory::where('code', 'EXC')->first();
        $truckCategory = EquipmentCategory::where('code', 'TRK')->first();

        Equipment::create([
            'equipment_number' => 'EQP-001',
            'name' => 'حفارة كاتربيلر 320',
            'name_en' => 'Caterpillar 320 Excavator',
            'description' => 'حفارة هيدروليكية متوسطة',
            'category_id' => $excavatorCategory->id,
            'brand' => 'Caterpillar',
            'model' => '320D',
            'year' => '2022',
            'serial_number' => 'CAT320-2022-001',
            'ownership' => 'owned',
            'purchase_price' => 450000,
            'current_value' => 420000,
            'hourly_rate' => 250,
            'daily_rate' => 2000,
            'operating_cost_per_hour' => 150,
            'capacity' => '1.2 متر مكعب',
            'power' => '121 حصان',
            'fuel_type' => 'ديزل',
            'fuel_consumption' => 15,
            'status' => 'available',
            'maintenance_interval_hours' => 250,
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_number' => 'EQP-002',
            'name' => 'شاحنة نقل ثقيل مرسيدس',
            'name_en' => 'Mercedes Heavy Truck',
            'description' => 'شاحنة نقل مواد بناء',
            'category_id' => $truckCategory->id,
            'brand' => 'Mercedes-Benz',
            'model' => 'Actros 2648',
            'year' => '2023',
            'plate_number' => 'ABC-1234',
            'ownership' => 'owned',
            'purchase_price' => 320000,
            'current_value' => 310000,
            'hourly_rate' => 180,
            'daily_rate' => 1500,
            'operating_cost_per_hour' => 100,
            'capacity' => '25 طن',
            'power' => '476 حصان',
            'fuel_type' => 'ديزل',
            'fuel_consumption' => 35,
            'status' => 'available',
            'maintenance_interval_hours' => 300,
            'is_active' => true,
        ]);
    }
}
