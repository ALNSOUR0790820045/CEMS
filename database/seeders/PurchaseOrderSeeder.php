<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\User;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user if none exists
        $user = User::firstOrCreate(
            ['email' => 'admin@cems.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create Suppliers
        $suppliers = [
            ['name' => 'شركة المواد الإنشائية', 'name_en' => 'Construction Materials Co', 'email' => 'info@construction.com', 'phone' => '0501234567', 'tax_number' => '300000000000003', 'is_active' => true],
            ['name' => 'مؤسسة الحديد والصلب', 'name_en' => 'Iron & Steel Est', 'email' => 'sales@ironsteel.com', 'phone' => '0502345678', 'tax_number' => '300000000000004', 'is_active' => true],
            ['name' => 'شركة الأسمنت الوطنية', 'name_en' => 'National Cement Co', 'email' => 'orders@cement.com', 'phone' => '0503456789', 'tax_number' => '300000000000005', 'is_active' => true],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create Warehouses
        $warehouses = [
            ['name' => 'المستودع الرئيسي', 'name_en' => 'Main Warehouse', 'code' => 'WH-001', 'location' => 'الرياض - حي الصناعية', 'manager' => 'أحمد محمد', 'phone' => '0504567890', 'is_active' => true],
            ['name' => 'مستودع جدة', 'name_en' => 'Jeddah Warehouse', 'code' => 'WH-002', 'location' => 'جدة - حي السليمانية', 'manager' => 'خالد علي', 'phone' => '0505678901', 'is_active' => true],
            ['name' => 'مستودع الدمام', 'name_en' => 'Dammam Warehouse', 'code' => 'WH-003', 'location' => 'الدمام - حي الفيصلية', 'manager' => 'محمد عبدالله', 'phone' => '0506789012', 'is_active' => true],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        // Create Payment Terms
        $paymentTerms = [
            ['name' => 'نقدي', 'name_en' => 'Cash', 'days' => 0, 'is_active' => true],
            ['name' => 'آجل 15 يوم', 'name_en' => 'Net 15', 'days' => 15, 'is_active' => true],
            ['name' => 'آجل 30 يوم', 'name_en' => 'Net 30', 'days' => 30, 'is_active' => true],
            ['name' => 'آجل 60 يوم', 'name_en' => 'Net 60', 'days' => 60, 'is_active' => true],
            ['name' => 'آجل 90 يوم', 'name_en' => 'Net 90', 'days' => 90, 'is_active' => true],
        ];

        foreach ($paymentTerms as $term) {
            PaymentTerm::create($term);
        }

        // Create Products
        $products = [
            ['name' => 'أسمنت بورتلاندي', 'name_en' => 'Portland Cement', 'sku' => 'CEM-001', 'description' => 'كيس 50 كجم', 'unit' => 'كيس', 'cost_price' => 25.00, 'selling_price' => 30.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'حديد تسليح 12mm', 'name_en' => 'Rebar 12mm', 'sku' => 'REB-12', 'description' => 'طن', 'unit' => 'طن', 'cost_price' => 2800.00, 'selling_price' => 3200.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'حديد تسليح 16mm', 'name_en' => 'Rebar 16mm', 'sku' => 'REB-16', 'description' => 'طن', 'unit' => 'طن', 'cost_price' => 2850.00, 'selling_price' => 3250.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'طوب أحمر', 'name_en' => 'Red Brick', 'sku' => 'BRK-001', 'description' => 'ألف طوبة', 'unit' => 'ألف', 'cost_price' => 280.00, 'selling_price' => 350.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'رمل', 'name_en' => 'Sand', 'sku' => 'SND-001', 'description' => 'متر مكعب', 'unit' => 'م³', 'cost_price' => 45.00, 'selling_price' => 60.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'حصى', 'name_en' => 'Gravel', 'sku' => 'GRV-001', 'description' => 'متر مكعب', 'unit' => 'م³', 'cost_price' => 50.00, 'selling_price' => 65.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'خشب لتزانة', 'name_en' => 'Plywood', 'sku' => 'PLY-001', 'description' => 'لوح 122x244 سم', 'unit' => 'لوح', 'cost_price' => 85.00, 'selling_price' => 110.00, 'tax_rate' => 15, 'is_active' => true],
            ['name' => 'مسامير', 'name_en' => 'Nails', 'sku' => 'NLS-001', 'description' => 'كيلو', 'unit' => 'كجم', 'cost_price' => 12.00, 'selling_price' => 18.00, 'tax_rate' => 15, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Purchase Order test data seeded successfully!');
    }
}

