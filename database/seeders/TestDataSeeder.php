<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Contract;
use App\Models\VariationOrder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user (or use existing)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'phone' => '0501234567',
                'is_active' => true,
                'language' => 'ar',
            ]
        );

        // Create a company (or use existing)
        $company = Company::firstOrCreate(
            ['slug' => 'advanced-construction'],
            [
                'name' => 'شركة الإنشاءات المتقدمة',
                'name_en' => 'Advanced Construction Company',
                'email' => 'info@advanced.com',
                'phone' => '0112345678',
                'country' => 'SA',
                'is_active' => true,
            ]
        );

        // Link user to company
        $user->update(['company_id' => $company->id]);

        // Create projects
        $project1 = Project::create([
            'name' => 'مشروع برج الرياض',
            'code' => 'PRJ001',
            'company_id' => $company->id,
            'description' => 'مشروع إنشاء برج سكني تجاري في الرياض',
            'start_date' => '2024-01-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
        ]);

        $project2 = Project::create([
            'name' => 'مشروع كورنيش جدة',
            'code' => 'PRJ002',
            'company_id' => $company->id,
            'description' => 'تطوير كورنيش جدة',
            'start_date' => '2024-06-01',
            'end_date' => '2025-12-31',
            'status' => 'active',
        ]);

        // Create contracts
        $contract1 = Contract::create([
            'contract_number' => 'CNT-001-2024',
            'project_id' => $project1->id,
            'title' => 'عقد الأعمال الإنشائية',
            'value' => 50000000,
            'start_date' => '2024-01-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
        ]);

        // Create variation orders
        $vo1 = VariationOrder::create([
            'vo_number' => 'temp-1', // Temporary, will be updated
            'project_id' => $project1->id,
            'contract_id' => $contract1->id,
            'sequence_number' => 1,
            'title' => 'إضافة طابق إضافي',
            'description' => 'إضافة طابق إضافي للبرج بناءً على طلب العميل',
            'justification' => 'زيادة الطلب على الوحدات السكنية',
            'type' => 'addition',
            'source' => 'client',
            'estimated_value' => 5000000,
            'currency' => 'SAR',
            'time_impact_days' => 90,
            'identification_date' => '2024-03-15',
            'status' => 'draft',
            'priority' => 'high',
            'requested_by' => $user->id,
        ]);

        $vo1->vo_number = $vo1->generateVoNumber();
        $vo1->save();
        $vo1->addTimelineEntry('Created', null, 'draft', 'Variation order created');

        $vo2 = VariationOrder::create([
            'vo_number' => 'temp-2', // Temporary, will be updated
            'project_id' => $project1->id,
            'contract_id' => $contract1->id,
            'sequence_number' => 2,
            'title' => 'تعديل نظام التكييف',
            'description' => 'تغيير نظام التكييف من مركزي إلى VRV',
            'justification' => 'توفير في استهلاك الطاقة',
            'type' => 'modification',
            'source' => 'consultant',
            'estimated_value' => 2000000,
            'quoted_value' => 1800000,
            'currency' => 'SAR',
            'time_impact_days' => 30,
            'identification_date' => '2024-04-01',
            'submission_date' => '2024-04-05',
            'status' => 'submitted',
            'priority' => 'medium',
            'requested_by' => $user->id,
        ]);

        $vo2->vo_number = $vo2->generateVoNumber();
        $vo2->save();
        $vo2->addTimelineEntry('Created', null, 'draft', 'Variation order created');
        $vo2->addTimelineEntry('Submitted', 'draft', 'submitted', 'Submitted for review');

        $vo3 = VariationOrder::create([
            'vo_number' => 'temp-3', // Temporary, will be updated
            'project_id' => $project2->id,
            'sequence_number' => 1,
            'title' => 'إضافة منطقة ألعاب مائية',
            'description' => 'إضافة منطقة ألعاب مائية للأطفال',
            'justification' => 'تحسين المرافق الترفيهية',
            'type' => 'addition',
            'source' => 'client',
            'estimated_value' => 3500000,
            'quoted_value' => 3200000,
            'approved_value' => 3000000,
            'currency' => 'SAR',
            'time_impact_days' => 60,
            'approved_extension_days' => 45,
            'extension_approved' => true,
            'identification_date' => '2024-07-01',
            'submission_date' => '2024-07-05',
            'approval_date' => '2024-07-15',
            'status' => 'approved',
            'priority' => 'high',
            'requested_by' => $user->id,
            'approved_by' => $user->id,
        ]);

        $vo3->vo_number = $vo3->generateVoNumber();
        $vo3->save();
        $vo3->addTimelineEntry('Created', null, 'draft', 'Variation order created');
        $vo3->addTimelineEntry('Submitted', 'draft', 'submitted', 'Submitted for review');
        $vo3->addTimelineEntry('Approved', 'submitted', 'approved', 'Approved with value of 3,000,000 SAR');

        $this->command->info('Test data created successfully!');
        $this->command->info('Email: test@example.com');
        $this->command->info('Password: password');
    }
}
