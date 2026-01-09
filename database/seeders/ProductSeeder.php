<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'أسمنت بورتلاند رمادي',
                'sku' => 'CEM-001',
                'description' => 'أسمنت بورتلاند رمادي عالي الجودة مطابق للمواصفات السعودية',
                'price' => 25.50,
                'cost' => 20.00,
                'unit' => 'كيس 50 كجم',
                'is_active' => true,
            ],
            [
                'name' => 'حديد تسليح 12 ملم',
                'sku' => 'STEEL-012',
                'description' => 'حديد تسليح قطر 12 ملم مطابق للمواصفات',
                'price' => 2850.00,
                'cost' => 2600.00,
                'unit' => 'طن',
                'is_active' => true,
            ],
            [
                'name' => 'بلوك خرساني 20x20x40',
                'sku' => 'BLK-2040',
                'description' => 'بلوك خرساني مقاس 20x20x40 سم',
                'price' => 3.50,
                'cost' => 2.80,
                'unit' => 'قطعة',
                'is_active' => true,
            ],
            [
                'name' => 'رمل مغسول',
                'sku' => 'SAND-001',
                'description' => 'رمل مغسول خالي من الشوائب',
                'price' => 80.00,
                'cost' => 65.00,
                'unit' => 'متر مكعب',
                'is_active' => true,
            ],
            [
                'name' => 'حصى مكسر',
                'sku' => 'GRAVEL-001',
                'description' => 'حصى مكسر مقاس 1-2 سم',
                'price' => 90.00,
                'cost' => 75.00,
                'unit' => 'متر مكعب',
                'is_active' => true,
            ],
            [
                'name' => 'طوب أحمر',
                'sku' => 'BRICK-001',
                'description' => 'طوب أحمر فخاري مقاس 6x12x25 سم',
                'price' => 0.85,
                'cost' => 0.65,
                'unit' => 'قطعة',
                'is_active' => true,
            ],
            [
                'name' => 'جبس بورد 12 ملم',
                'sku' => 'GYPS-012',
                'description' => 'ألواح جبس بورد سماكة 12 ملم مقاس 120x240 سم',
                'price' => 45.00,
                'cost' => 35.00,
                'unit' => 'لوح',
                'is_active' => true,
            ],
            [
                'name' => 'دهان أكريليك أبيض',
                'sku' => 'PAINT-WHT',
                'description' => 'دهان أكريليك داخلي وخارجي لون أبيض',
                'price' => 120.00,
                'cost' => 95.00,
                'unit' => 'جالون 20 لتر',
                'is_active' => true,
            ],
            [
                'name' => 'بلاط سيراميك 60x60',
                'sku' => 'TILE-6060',
                'description' => 'بلاط سيراميك مصقول مقاس 60x60 سم',
                'price' => 28.00,
                'cost' => 22.00,
                'unit' => 'متر مربع',
                'is_active' => true,
            ],
            [
                'name' => 'كابل كهربائي 2.5 ملم',
                'sku' => 'CABLE-2.5',
                'description' => 'كابل كهربائي نحاسي 2.5 ملم مربع',
                'price' => 4.50,
                'cost' => 3.50,
                'unit' => 'متر',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
