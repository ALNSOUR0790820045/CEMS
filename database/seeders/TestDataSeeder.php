<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectWbs;
use App\Models\BoqItem;
use App\Models\ChangeOrder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@cems.com',
            'password' => Hash::make('password'),
            'company_id' => null,
        ]);

        // Create a company
        $company = Company::create([
            'name' => 'شركة المقاولات الرئيسية',
            'name_en' => 'Main Contracting Company',
            'slug' => 'main-contracting',
            'email' => 'info@company.com',
            'phone' => '0123456789',
            'address' => 'الرياض',
            'city' => 'الرياض',
            'country' => 'SA',
            'commercial_registration' => '1234567890',
            'tax_number' => '300000000000003',
            'is_active' => true,
        ]);

        // Update user with company
        $user->update(['company_id' => $company->id]);

        // Create a project
        $project = Project::create([
            'company_id' => $company->id,
            'project_number' => 'PRJ-001',
            'name' => 'مشروع إنشاء مبنى إداري',
            'name_en' => 'Administrative Building Construction Project',
            'description' => 'مشروع إنشاء مبنى إداري متكامل',
            'location' => 'الرياض - حي الملقا',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'contract_value' => 5000000.00,
            'status' => 'active',
            'project_manager_id' => $user->id,
        ]);

        // Create WBS
        $wbs1 = ProjectWbs::create([
            'project_id' => $project->id,
            'parent_id' => null,
            'wbs_code' => 'WBS-001',
            'name' => 'الأعمال الإنشائية',
            'description' => 'أعمال الخرسانة والحديد',
            'level' => 1,
            'sort_order' => 1,
        ]);

        $wbs2 = ProjectWbs::create([
            'project_id' => $project->id,
            'parent_id' => null,
            'wbs_code' => 'WBS-002',
            'name' => 'أعمال التشطيبات',
            'description' => 'أعمال التشطيب والدهانات',
            'level' => 1,
            'sort_order' => 2,
        ]);

        // Create BOQ Items
        BoqItem::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs1->id,
            'item_code' => 'BOQ-001',
            'description' => 'أعمال الحفر والردم',
            'unit' => 'م³',
            'quantity' => 1000.000,
            'unit_price' => 50.00,
            'total_price' => 50000.00,
            'sort_order' => 1,
        ]);

        BoqItem::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs1->id,
            'item_code' => 'BOQ-002',
            'description' => 'أعمال الخرسانة المسلحة',
            'unit' => 'م³',
            'quantity' => 500.000,
            'unit_price' => 800.00,
            'total_price' => 400000.00,
            'sort_order' => 2,
        ]);

        BoqItem::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs1->id,
            'item_code' => 'BOQ-003',
            'description' => 'أعمال حديد التسليح',
            'unit' => 'طن',
            'quantity' => 80.000,
            'unit_price' => 4500.00,
            'total_price' => 360000.00,
            'sort_order' => 3,
        ]);

        BoqItem::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs2->id,
            'item_code' => 'BOQ-004',
            'description' => 'أعمال البياض الداخلي',
            'unit' => 'م²',
            'quantity' => 3000.000,
            'unit_price' => 35.00,
            'total_price' => 105000.00,
            'sort_order' => 4,
        ]);

        BoqItem::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs2->id,
            'item_code' => 'BOQ-005',
            'description' => 'أعمال الدهانات',
            'unit' => 'م²',
            'quantity' => 3000.000,
            'unit_price' => 25.00,
            'total_price' => 75000.00,
            'sort_order' => 5,
        ]);

        // Create Change Orders
        ChangeOrder::create([
            'project_id' => $project->id,
            'co_number' => 'CO-001',
            'description' => 'إضافة أعمال إضافية للبدروم',
            'amount' => 150000.00,
            'type' => 'addition',
            'status' => 'approved',
            'approval_date' => now(),
            'approved_by' => $user->id,
        ]);

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: admin@cems.com');
        $this->command->info('Password: password');
    }
}
