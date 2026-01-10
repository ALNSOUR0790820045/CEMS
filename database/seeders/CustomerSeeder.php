<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'شركة المستقبل للتطوير العقاري',
                'email' => 'info@future-real-estate.sa',
                'phone' => '+966 11 234 5678',
                'address' => 'طريق الملك فهد، الرياض',
                'city' => 'الرياض',
                'country' => 'SA',
                'tax_number' => '300123456700003',
            ],
            [
                'name' => 'مؤسسة البناء الحديث',
                'email' => 'contact@modern-build.sa',
                'phone' => '+966 12 345 6789',
                'address' => 'شارع الأمير سلطان، جدة',
                'city' => 'جدة',
                'country' => 'SA',
                'tax_number' => '300234567800003',
            ],
            [
                'name' => 'شركة الخليج للمقاولات',
                'email' => 'info@gulf-contracting.sa',
                'phone' => '+966 13 456 7890',
                'address' => 'طريق الظهران، الدمام',
                'city' => 'الدمام',
                'country' => 'SA',
                'tax_number' => '300345678900003',
            ],
            [
                'name' => 'مؤسسة النخبة للاستثمار',
                'email' => 'elite@investment.sa',
                'phone' => '+966 11 567 8901',
                'address' => 'حي العليا، الرياض',
                'city' => 'الرياض',
                'country' => 'SA',
                'tax_number' => '300456789000003',
            ],
            [
                'name' => 'شركة الأمل للتجارة والمقاولات',
                'email' => 'info@amal-trading.sa',
                'phone' => '+966 12 678 9012',
                'address' => 'طريق المدينة، جدة',
                'city' => 'جدة',
                'country' => 'SA',
                'tax_number' => '300567890100003',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
