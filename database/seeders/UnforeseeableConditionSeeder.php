<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Contract;
use App\Models\UnforeseeableCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnforeseeableConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user if none exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }

        // Create a test project
        $project = Project::create([
            'name' => 'مشروع الطريق الصحراوي - Desert Highway Project',
            'code' => 'PRJ-2026-001',
        ]);

        // Create a test contract
        $contract = Contract::create([
            'name' => 'عقد الطريق الرئيسي - Main Road Contract',
            'contract_number' => 'CTR-2026-001',
        ]);

        // Create sample unforeseeable conditions
        UnforeseeableCondition::create([
            'condition_number' => 'UFC-2026-0001',
            'project_id' => $project->id,
            'contract_id' => $contract->id,
            'title' => 'وجود طبقة صخرية غير متوقعة - Unexpected Rock Layer',
            'description' => 'تم اكتشاف طبقة صخرية صلبة غير متوقعة على عمق 3 أمتار من سطح الأرض، مما يتطلب معدات حفر متخصصة.',
            'location' => 'KM 15+200 - الكيلومتر 15+200',
            'location_latitude' => 31.9539,
            'location_longitude' => 35.9106,
            'condition_type' => 'rock_conditions',
            'discovery_date' => now()->subDays(15),
            'notice_date' => now()->subDays(14),
            'contractual_clause' => '4.12',
            'impact_description' => 'تأخير في أعمال الحفر وزيادة التكاليف بسبب الحاجة لمعدات متخصصة',
            'estimated_delay_days' => 10,
            'estimated_cost_impact' => 25000.00,
            'currency' => 'JOD',
            'tender_assumptions' => 'كانت الدراسات الأولية تشير إلى تربة طينية على عمق 5 أمتار',
            'site_investigation_data' => 'تقرير الجسات الأولي (SI-2025-001) أشار إلى تربة طينية فقط',
            'actual_conditions' => 'طبقة صخرية بازلتية صلبة على عمق 3-5 أمتار',
            'difference_analysis' => 'الفرق الجوهري بين المتوقع والفعلي يتطلب تغيير طريقة التنفيذ والمعدات',
            'immediate_measures' => 'إيقاف العمل وطلب معدات حفر صخري متخصصة',
            'proposed_solution' => 'استخدام كسارات هيدروليكية ومثاقب صخرية',
            'status' => 'notice_sent',
            'reported_by' => $user->id,
            'notes' => 'يتطلب موافقة المهندس الاستشاري على تغيير طريقة التنفيذ',
        ]);

        UnforeseeableCondition::create([
            'condition_number' => 'UFC-2026-0002',
            'project_id' => $project->id,
            'contract_id' => $contract->id,
            'title' => 'تواجد خطوط خدمات غير موثقة - Undocumented Underground Utilities',
            'description' => 'اكتشاف خطوط كهرباء وماء غير موثقة في الخرائط المقدمة',
            'location' => 'KM 20+450',
            'location_latitude' => 31.9639,
            'location_longitude' => 35.9206,
            'condition_type' => 'underground_utilities',
            'discovery_date' => now()->subDays(5),
            'contractual_clause' => '4.12',
            'impact_description' => 'تأخير الأعمال حتى يتم نقل الخطوط من قبل الجهات المختصة',
            'estimated_delay_days' => 20,
            'estimated_cost_impact' => 15000.00,
            'currency' => 'JOD',
            'tender_assumptions' => 'الخرائط المقدمة لم تظهر أي خدمات في هذا الموقع',
            'site_investigation_data' => 'لا توجد إشارة في المخططات المقدمة من المالك',
            'actual_conditions' => 'خط كهرباء 11 كيلو فولت وخط ماء قطر 200 مم',
            'difference_analysis' => 'وجود خدمات غير موثقة يشكل خطر على السلامة ويتطلب إعادة تخطيط',
            'immediate_measures' => 'إيقاف العمل فوراً والتنسيق مع شركة الكهرباء والمياه',
            'proposed_solution' => 'نقل الخطوط بواسطة الجهات المختصة',
            'status' => 'identified',
            'reported_by' => $user->id,
        ]);
    }
}
