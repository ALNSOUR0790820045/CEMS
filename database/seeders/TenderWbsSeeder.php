<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderWbs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenderWbsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company if it doesn't exist
        $company = Company::firstOrCreate(
            ['slug' => 'test-company'],
            [
                'name' => 'شركة الاختبار للمقاولات',
                'name_en' => 'Test Contracting Company',
                'email' => 'test@company.com',
                'phone' => '0500000000',
                'city' => 'الرياض',
                'country' => 'SA',
                'is_active' => true,
            ]
        );

        // Create a test tender
        $tender = Tender::firstOrCreate(
            ['reference_number' => 'TND-2026-001'],
            [
                'name' => 'مشروع إنشاء مجمع سكني',
                'name_en' => 'Residential Complex Construction Project',
                'description' => 'مشروع إنشاء مجمع سكني متكامل يتضمن 50 وحدة سكنية',
                'issue_date' => now(),
                'submission_deadline' => now()->addDays(30),
                'budget' => 50000000,
                'status' => 'published',
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );

        // Clear existing WBS items for this tender
        TenderWbs::where('tender_id', $tender->id)->delete();

        // Level 1: Main work packages
        $siteWorks = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.0',
            'name' => 'أعمال الموقع',
            'name_en' => 'Site Works',
            'level' => 1,
            'parent_id' => null,
            'sort_order' => 1,
            'estimated_cost' => 7500000,
            'weight_percentage' => 15,
            'is_summary' => true,
            'is_active' => true,
        ]);

        $structure = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '2.0',
            'name' => 'الهيكل الإنشائي',
            'name_en' => 'Structural Works',
            'level' => 1,
            'parent_id' => null,
            'sort_order' => 2,
            'estimated_cost' => 20000000,
            'weight_percentage' => 40,
            'is_summary' => true,
            'is_active' => true,
        ]);

        $electrical = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '3.0',
            'name' => 'الأعمال الكهربائية',
            'name_en' => 'Electrical Works',
            'level' => 1,
            'parent_id' => null,
            'sort_order' => 3,
            'estimated_cost' => 10000000,
            'weight_percentage' => 20,
            'is_summary' => true,
            'is_active' => true,
        ]);

        $mechanical = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '4.0',
            'name' => 'الأعمال الميكانيكية',
            'name_en' => 'Mechanical Works',
            'level' => 1,
            'parent_id' => null,
            'sort_order' => 4,
            'estimated_cost' => 7500000,
            'weight_percentage' => 15,
            'is_summary' => true,
            'is_active' => true,
        ]);

        $finishing = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '5.0',
            'name' => 'التشطيبات',
            'name_en' => 'Finishing Works',
            'level' => 1,
            'parent_id' => null,
            'sort_order' => 5,
            'estimated_cost' => 5000000,
            'weight_percentage' => 10,
            'is_summary' => true,
            'is_active' => true,
        ]);

        // Level 2: Site Works breakdown
        $preparation = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.1',
            'name' => 'التحضيرات',
            'name_en' => 'Preparation',
            'level' => 2,
            'parent_id' => $siteWorks->id,
            'sort_order' => 1,
            'estimated_cost' => 2500000,
            'weight_percentage' => 5,
            'is_summary' => true,
            'is_active' => true,
        ]);

        $infrastructure = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2',
            'name' => 'البنية التحتية',
            'name_en' => 'Infrastructure',
            'level' => 2,
            'parent_id' => $siteWorks->id,
            'sort_order' => 2,
            'estimated_cost' => 5000000,
            'weight_percentage' => 10,
            'is_summary' => true,
            'is_active' => true,
        ]);

        // Level 3: Preparation breakdown
        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.1.1',
            'name' => 'المسح والترسيم',
            'name_en' => 'Survey and Layout',
            'level' => 3,
            'parent_id' => $preparation->id,
            'sort_order' => 1,
            'estimated_cost' => 1000000,
            'materials_cost' => 200000,
            'labor_cost' => 500000,
            'equipment_cost' => 300000,
            'weight_percentage' => 2,
            'estimated_duration_days' => 15,
            'is_summary' => false,
            'is_active' => true,
        ]);

        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.1.2',
            'name' => 'الحفريات',
            'name_en' => 'Excavation',
            'level' => 3,
            'parent_id' => $preparation->id,
            'sort_order' => 2,
            'estimated_cost' => 1500000,
            'materials_cost' => 100000,
            'labor_cost' => 600000,
            'equipment_cost' => 800000,
            'weight_percentage' => 3,
            'estimated_duration_days' => 20,
            'is_summary' => false,
            'is_active' => true,
        ]);

        // Level 3: Infrastructure breakdown
        $foundations = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.1',
            'name' => 'الأساسات',
            'name_en' => 'Foundations',
            'level' => 3,
            'parent_id' => $infrastructure->id,
            'sort_order' => 1,
            'estimated_cost' => 3000000,
            'weight_percentage' => 6,
            'is_summary' => true,
            'is_active' => true,
        ]);

        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.2',
            'name' => 'الشبكات',
            'name_en' => 'Networks',
            'level' => 3,
            'parent_id' => $infrastructure->id,
            'sort_order' => 2,
            'estimated_cost' => 2000000,
            'materials_cost' => 1000000,
            'labor_cost' => 700000,
            'equipment_cost' => 300000,
            'weight_percentage' => 4,
            'estimated_duration_days' => 30,
            'is_summary' => false,
            'is_active' => true,
        ]);

        // Level 4: Foundations breakdown
        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.1.1',
            'name' => 'حفر الأساسات',
            'name_en' => 'Foundation Excavation',
            'level' => 4,
            'parent_id' => $foundations->id,
            'sort_order' => 1,
            'estimated_cost' => 1000000,
            'materials_cost' => 50000,
            'labor_cost' => 400000,
            'equipment_cost' => 550000,
            'weight_percentage' => 2,
            'estimated_duration_days' => 10,
            'is_summary' => false,
            'is_active' => true,
        ]);

        $concrete = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.1.2',
            'name' => 'صب الخرسانة',
            'name_en' => 'Concrete Pouring',
            'level' => 4,
            'parent_id' => $foundations->id,
            'sort_order' => 2,
            'estimated_cost' => 2000000,
            'weight_percentage' => 4,
            'is_summary' => true,
            'is_active' => true,
        ]);

        // Level 5: Concrete breakdown (maximum level)
        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.1.2.1',
            'name' => 'تحضير القوالب',
            'name_en' => 'Formwork Preparation',
            'level' => 5,
            'parent_id' => $concrete->id,
            'sort_order' => 1,
            'estimated_cost' => 500000,
            'materials_cost' => 200000,
            'labor_cost' => 300000,
            'weight_percentage' => 1,
            'estimated_duration_days' => 5,
            'is_summary' => false,
            'is_active' => true,
        ]);

        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '1.2.1.2.2',
            'name' => 'صب الخرسانة المسلحة',
            'name_en' => 'Reinforced Concrete Pouring',
            'level' => 5,
            'parent_id' => $concrete->id,
            'sort_order' => 2,
            'estimated_cost' => 1500000,
            'materials_cost' => 1000000,
            'labor_cost' => 400000,
            'equipment_cost' => 100000,
            'weight_percentage' => 3,
            'estimated_duration_days' => 10,
            'is_summary' => false,
            'is_active' => true,
        ]);

        // Level 2: Structure breakdown
        $concreteStructure = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '2.1',
            'name' => 'الهيكل الخرساني',
            'name_en' => 'Concrete Structure',
            'level' => 2,
            'parent_id' => $structure->id,
            'sort_order' => 1,
            'estimated_cost' => 15000000,
            'materials_cost' => 10000000,
            'labor_cost' => 4000000,
            'equipment_cost' => 1000000,
            'weight_percentage' => 30,
            'estimated_duration_days' => 120,
            'is_summary' => false,
            'is_active' => true,
        ]);

        TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => '2.2',
            'name' => 'الهيكل المعدني',
            'name_en' => 'Steel Structure',
            'level' => 2,
            'parent_id' => $structure->id,
            'sort_order' => 2,
            'estimated_cost' => 5000000,
            'materials_cost' => 3500000,
            'labor_cost' => 1000000,
            'equipment_cost' => 500000,
            'weight_percentage' => 10,
            'estimated_duration_days' => 60,
            'is_summary' => false,
            'is_active' => true,
        ]);

        $this->command->info('WBS test data seeded successfully!');
    }
}
