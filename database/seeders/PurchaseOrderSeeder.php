<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a company if it doesn't exist
        $company = Company::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name' => 'Demo Company',
                'name_en' => 'Demo Company',
                'country' => 'US',
                'is_active' => true,
            ]
        );

        // Create a user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );

        // Create currencies
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
        ];

        foreach ($currencies as $currencyData) {
            Currency::firstOrCreate(
                ['code' => $currencyData['code']],
                array_merge($currencyData, ['is_active' => true])
            );
        }

        // Create units
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'PC'],
            ['name' => 'Kilogram', 'abbreviation' => 'KG'],
            ['name' => 'Meter', 'abbreviation' => 'M'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Box', 'abbreviation' => 'BOX'],
        ];

        foreach ($units as $unitData) {
            Unit::firstOrCreate(
                ['abbreviation' => $unitData['abbreviation'], 'company_id' => $company->id],
                array_merge($unitData, ['company_id' => $company->id])
            );
        }

        // Create vendors
        $vendors = [
            [
                'vendor_code' => 'VEN-001',
                'name' => 'ABC Supplies Co.',
                'email' => 'contact@abcsupplies.com',
                'phone' => '+1234567890',
                'city' => 'New York',
                'country' => 'US',
                'payment_terms' => 'Net 30',
            ],
            [
                'vendor_code' => 'VEN-002',
                'name' => 'XYZ Materials Ltd.',
                'email' => 'sales@xyzmaterials.com',
                'phone' => '+9876543210',
                'city' => 'London',
                'country' => 'GB',
                'payment_terms' => 'Net 60',
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::firstOrCreate(
                ['vendor_code' => $vendorData['vendor_code']],
                array_merge($vendorData, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // Create projects
        $projects = [
            [
                'project_code' => 'PRJ-001',
                'name' => 'Office Renovation',
                'description' => 'Main office building renovation project',
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-002',
                'name' => 'Warehouse Expansion',
                'description' => 'Expansion of warehouse facility',
                'start_date' => now()->addMonth(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::firstOrCreate(
                ['project_code' => $projectData['project_code']],
                array_merge($projectData, ['company_id' => $company->id])
            );
        }

        // Create materials
        $pcUnit = Unit::where('abbreviation', 'PC')->where('company_id', $company->id)->first();
        $kgUnit = Unit::where('abbreviation', 'KG')->where('company_id', $company->id)->first();
        $mUnit = Unit::where('abbreviation', 'M')->where('company_id', $company->id)->first();

        $materials = [
            [
                'material_code' => 'MAT-001',
                'name' => 'Steel Beam - 6m',
                'description' => 'Structural steel beam 6 meters length',
                'unit_id' => $pcUnit->id,
                'unit_price' => 250.00,
            ],
            [
                'material_code' => 'MAT-002',
                'name' => 'Cement - Portland',
                'description' => '50kg bag of Portland cement',
                'unit_id' => $kgUnit->id,
                'unit_price' => 8.50,
            ],
            [
                'material_code' => 'MAT-003',
                'name' => 'Electrical Cable',
                'description' => '2.5mm electrical cable',
                'unit_id' => $mUnit->id,
                'unit_price' => 1.25,
            ],
        ];

        foreach ($materials as $materialData) {
            Material::firstOrCreate(
                ['material_code' => $materialData['material_code']],
                array_merge($materialData, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // Create sample purchase orders
        $vendor1 = Vendor::where('vendor_code', 'VEN-001')->first();
        $vendor2 = Vendor::where('vendor_code', 'VEN-002')->first();
        $usdCurrency = Currency::where('code', 'USD')->first();
        $project1 = Project::where('project_code', 'PRJ-001')->first();
        $material1 = Material::where('material_code', 'MAT-001')->first();
        $material2 = Material::where('material_code', 'MAT-002')->first();

        // PO 1 - Draft
        $po1 = PurchaseOrder::create([
            'po_number' => 'PO-' . date('Y') . '-0001',
            'po_date' => now(),
            'vendor_id' => $vendor1->id,
            'project_id' => $project1->id,
            'delivery_date' => now()->addDays(14),
            'delivery_location' => 'Main Office - 123 Business St, New York, NY',
            'payment_terms' => 'Net 30',
            'currency_id' => $usdCurrency->id,
            'exchange_rate' => 1.0000,
            'status' => 'draft',
            'notes' => 'Initial order for office renovation project',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        $po1->items()->create([
            'material_id' => $material1->id,
            'quantity' => 20,
            'unit_id' => $material1->unit_id,
            'unit_price' => 250.00,
            'tax_rate' => 10.00,
            'discount_rate' => 5.00,
        ]);

        $po1->items()->create([
            'material_id' => $material2->id,
            'quantity' => 100,
            'unit_id' => $material2->unit_id,
            'unit_price' => 8.50,
            'tax_rate' => 10.00,
            'discount_rate' => 0,
        ]);

        $po1->calculateTotals();

        // PO 2 - Submitted for approval
        $po2 = PurchaseOrder::create([
            'po_number' => 'PO-' . date('Y') . '-0002',
            'po_date' => now()->subDays(2),
            'vendor_id' => $vendor2->id,
            'delivery_date' => now()->addDays(21),
            'delivery_location' => 'Warehouse - 456 Storage Rd, Brooklyn, NY',
            'payment_terms' => 'Net 60',
            'currency_id' => $usdCurrency->id,
            'exchange_rate' => 1.0000,
            'status' => 'submitted',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        $po2->items()->create([
            'material_id' => $material1->id,
            'quantity' => 50,
            'unit_id' => $material1->unit_id,
            'unit_price' => 245.00,
            'tax_rate' => 10.00,
            'discount_rate' => 10.00,
        ]);

        $po2->calculateTotals();

        // PO 3 - Approved
        $po3 = PurchaseOrder::create([
            'po_number' => 'PO-' . date('Y') . '-0003',
            'po_date' => now()->subDays(5),
            'vendor_id' => $vendor1->id,
            'project_id' => $project1->id,
            'delivery_date' => now()->addDays(10),
            'payment_terms' => 'Net 30',
            'currency_id' => $usdCurrency->id,
            'exchange_rate' => 1.0000,
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'approved_at' => now()->subDays(4),
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        $po3->items()->create([
            'material_id' => $material2->id,
            'quantity' => 500,
            'unit_id' => $material2->unit_id,
            'unit_price' => 8.00,
            'tax_rate' => 10.00,
        ]);

        $po3->calculateTotals();

        $this->command->info('Purchase Order seeder completed successfully!');
        $this->command->info('Created sample data for testing the Purchase Orders module.');
    }
}
