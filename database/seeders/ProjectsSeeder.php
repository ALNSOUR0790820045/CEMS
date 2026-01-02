<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectWbs;
use App\Models\ProjectActivity;
use App\Models\ActivityDependency;
use App\Models\ProjectMilestone;
use Carbon\Carbon;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a company
        $company = Company::first();
        if (!$company) {
            $company = Company::create([
                'name' => 'شركة المقاولات العامة',
                'name_en' => 'General Contracting Company',
                'email' => 'info@gcc.com',
                'phone' => '966501234567',
                'country' => 'SA',
                'is_active' => true,
            ]);
        }

        // Get or create a user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'أحمد محمد',
                'email' => 'admin@gcc.com',
                'password' => bcrypt('password'), // WARNING: For development only! Change in production.
                'company_id' => $company->id,
                'is_active' => true,
            ]);
        }

        // Create a project
        $project = Project::create([
            'company_id' => $company->id,
            'project_code' => 'PRJ-001',
            'name' => 'مشروع برج الخليج التجاري',
            'name_en' => 'Gulf Commercial Tower Project',
            'description' => 'مشروع إنشاء برج تجاري متعدد الطوابق في قلب المدينة',
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->addMonths(18),
            'status' => 'active',
            'budget' => 50000000,
            'manager_id' => $user->id,
        ]);

        // Create WBS structure
        $wbs1 = ProjectWbs::create([
            'project_id' => $project->id,
            'wbs_code' => '1.0',
            'name' => 'الأعمال التمهيدية',
            'name_en' => 'Preliminary Works',
            'level' => 1,
            'order' => 1,
        ]);

        $wbs1_1 = ProjectWbs::create([
            'project_id' => $project->id,
            'parent_id' => $wbs1->id,
            'wbs_code' => '1.1',
            'name' => 'أعمال الحفر',
            'name_en' => 'Excavation Works',
            'level' => 2,
            'order' => 1,
        ]);

        $wbs2 = ProjectWbs::create([
            'project_id' => $project->id,
            'wbs_code' => '2.0',
            'name' => 'الهيكل الإنشائي',
            'name_en' => 'Structural Works',
            'level' => 1,
            'order' => 2,
        ]);

        $wbs2_1 = ProjectWbs::create([
            'project_id' => $project->id,
            'parent_id' => $wbs2->id,
            'wbs_code' => '2.1',
            'name' => 'الأساسات',
            'name_en' => 'Foundations',
            'level' => 2,
            'order' => 1,
        ]);

        // Create activities
        $activities = [];

        $activities[] = ProjectActivity::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs1_1->id,
            'activity_code' => 'ACT-001',
            'name' => 'مسح الموقع والتجهيز',
            'name_en' => 'Site Survey and Preparation',
            'description' => 'إجراء مسح شامل للموقع وتجهيز المعدات اللازمة',
            'planned_start_date' => Carbon::now()->subMonths(6),
            'planned_end_date' => Carbon::now()->subMonths(6)->addDays(7),
            'planned_duration_days' => 7,
            'actual_start_date' => Carbon::now()->subMonths(6),
            'actual_end_date' => Carbon::now()->subMonths(6)->addDays(7),
            'actual_duration_days' => 7,
            'planned_effort_hours' => 168,
            'actual_effort_hours' => 175,
            'progress_percent' => 100,
            'progress_method' => 'manual',
            'type' => 'task',
            'is_critical' => true,
            'responsible_id' => $user->id,
            'status' => 'completed',
            'budgeted_cost' => 50000,
            'actual_cost' => 52000,
            'priority' => 'high',
        ]);

        $activities[] = ProjectActivity::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs1_1->id,
            'activity_code' => 'ACT-002',
            'name' => 'أعمال الحفر الرئيسية',
            'name_en' => 'Main Excavation Works',
            'description' => 'حفر الموقع للأساسات والطوابق السفلية',
            'planned_start_date' => Carbon::now()->subMonths(6)->addDays(7),
            'planned_end_date' => Carbon::now()->subMonths(5)->addDays(7),
            'planned_duration_days' => 30,
            'actual_start_date' => Carbon::now()->subMonths(6)->addDays(7),
            'actual_end_date' => Carbon::now()->subMonths(5)->addDays(10),
            'actual_duration_days' => 33,
            'planned_effort_hours' => 720,
            'actual_effort_hours' => 792,
            'progress_percent' => 100,
            'progress_method' => 'duration',
            'type' => 'task',
            'is_critical' => true,
            'responsible_id' => $user->id,
            'status' => 'completed',
            'budgeted_cost' => 250000,
            'actual_cost' => 275000,
            'priority' => 'critical',
        ]);

        $activities[] = ProjectActivity::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs2_1->id,
            'activity_code' => 'ACT-003',
            'name' => 'صب الخرسانة للأساسات',
            'name_en' => 'Concrete Pouring for Foundations',
            'description' => 'صب وتسوية الخرسانة للأساسات',
            'planned_start_date' => Carbon::now()->subMonths(5)->addDays(10),
            'planned_end_date' => Carbon::now()->subMonths(4)->addDays(10),
            'planned_duration_days' => 30,
            'actual_start_date' => Carbon::now()->subMonths(5)->addDays(10),
            'actual_end_date' => null,
            'actual_duration_days' => null,
            'planned_effort_hours' => 600,
            'actual_effort_hours' => 420,
            'progress_percent' => 70,
            'progress_method' => 'effort',
            'type' => 'task',
            'is_critical' => true,
            'responsible_id' => $user->id,
            'status' => 'in_progress',
            'budgeted_cost' => 500000,
            'actual_cost' => 350000,
            'priority' => 'critical',
        ]);

        $activities[] = ProjectActivity::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs2->id,
            'activity_code' => 'ACT-004',
            'name' => 'أعمال الحدادة للأعمدة',
            'name_en' => 'Reinforcement for Columns',
            'description' => 'تركيب حديد التسليح للأعمدة',
            'planned_start_date' => Carbon::now()->subMonths(4)->addDays(10),
            'planned_end_date' => Carbon::now()->subMonths(3)->addDays(10),
            'planned_duration_days' => 30,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'actual_duration_days' => null,
            'planned_effort_hours' => 480,
            'actual_effort_hours' => 0,
            'progress_percent' => 0,
            'progress_method' => 'manual',
            'type' => 'task',
            'is_critical' => false,
            'responsible_id' => $user->id,
            'status' => 'not_started',
            'budgeted_cost' => 300000,
            'actual_cost' => 0,
            'priority' => 'high',
        ]);

        $activities[] = ProjectActivity::create([
            'project_id' => $project->id,
            'wbs_id' => $wbs2->id,
            'activity_code' => 'ACT-005',
            'name' => 'صب الأعمدة',
            'name_en' => 'Columns Concrete Pouring',
            'description' => 'صب الخرسانة للأعمدة',
            'planned_start_date' => Carbon::now()->subMonths(3)->addDays(10),
            'planned_end_date' => Carbon::now()->subMonths(2)->addDays(10),
            'planned_duration_days' => 30,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'actual_duration_days' => null,
            'planned_effort_hours' => 400,
            'actual_effort_hours' => 0,
            'progress_percent' => 0,
            'progress_method' => 'duration',
            'type' => 'task',
            'is_critical' => false,
            'responsible_id' => $user->id,
            'status' => 'not_started',
            'budgeted_cost' => 400000,
            'actual_cost' => 0,
            'priority' => 'medium',
        ]);

        // Create dependencies
        ActivityDependency::create([
            'predecessor_id' => $activities[0]->id,
            'successor_id' => $activities[1]->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        ActivityDependency::create([
            'predecessor_id' => $activities[1]->id,
            'successor_id' => $activities[2]->id,
            'type' => 'FS',
            'lag_days' => 3,
        ]);

        ActivityDependency::create([
            'predecessor_id' => $activities[2]->id,
            'successor_id' => $activities[3]->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        ActivityDependency::create([
            'predecessor_id' => $activities[3]->id,
            'successor_id' => $activities[4]->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        // Create milestones
        ProjectMilestone::create([
            'project_id' => $project->id,
            'activity_id' => $activities[1]->id,
            'name' => 'اكتمال أعمال الحفر',
            'description' => 'إنهاء جميع أعمال الحفر والبدء بالأساسات',
            'target_date' => Carbon::now()->subMonths(5)->addDays(7),
            'actual_date' => Carbon::now()->subMonths(5)->addDays(10),
            'status' => 'achieved',
            'type' => 'project',
            'is_critical' => true,
            'deliverables' => 'تقرير الحفر النهائي، شهادة المختبر',
        ]);

        ProjectMilestone::create([
            'project_id' => $project->id,
            'activity_id' => $activities[2]->id,
            'name' => 'اكتمال الأساسات',
            'description' => 'إنهاء أعمال الأساسات والبدء بالأعمدة',
            'target_date' => Carbon::now()->subMonths(4)->addDays(10),
            'actual_date' => null,
            'status' => 'pending',
            'type' => 'project',
            'is_critical' => true,
            'deliverables' => 'تقرير الأساسات، شهادة فحص الخرسانة',
        ]);

        ProjectMilestone::create([
            'project_id' => $project->id,
            'activity_id' => null,
            'name' => 'دفعة الدفع الأولى',
            'description' => 'استحقاق الدفعة الأولى من المستخلص',
            'target_date' => Carbon::now()->subMonths(4),
            'actual_date' => Carbon::now()->subMonths(4)->addDays(5),
            'status' => 'achieved',
            'type' => 'payment',
            'is_critical' => false,
            'deliverables' => 'مستخلص رقم 1، شهادة الاستلام المؤقت',
        ]);
    }
}

