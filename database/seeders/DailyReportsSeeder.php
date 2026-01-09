<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\DailyReport;
use App\Models\User;

class DailyReportsSeeder extends Seeder
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
                'name' => 'شركة الإنشاءات الحديثة',
                'name_en' => 'Modern Construction Company',
                'slug' => 'modern-construction',
                'email' => 'info@modernconst.sa',
                'phone' => '+966501234567',
                'address' => 'الرياض، المملكة العربية السعودية',
                'city' => 'الرياض',
                'country' => 'SA',
                'commercial_registration' => '1234567890',
                'tax_number' => '300123456789003',
                'is_active' => true,
            ]);
        }

        // Create sample projects
        $projects = [];
        $projectData = [
            ['name' => 'مشروع برج الأعمال', 'code' => 'PRJ-2026-001'],
            ['name' => 'مشروع المجمع السكني', 'code' => 'PRJ-2026-002'],
            ['name' => 'مشروع الطريق السريع', 'code' => 'PRJ-2026-003'],
        ];

        foreach ($projectData as $data) {
            $projects[] = Project::create([
                'company_id' => $company->id,
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => 'مشروع إنشائي متكامل',
                'status' => 'active',
            ]);
        }

        // Create activities for each project
        $activityNames = [
            'أعمال الحفر والأساسات',
            'صب الخرسانة',
            'أعمال الطوب والبلوك',
            'أعمال الكهرباء',
            'أعمال السباكة',
            'أعمال التشطيبات',
        ];

        foreach ($projects as $project) {
            foreach ($activityNames as $activityName) {
                ProjectActivity::create([
                    'project_id' => $project->id,
                    'name' => $activityName,
                    'description' => 'نشاط مشروع',
                ]);
            }
        }

        // Get users for signatures
        $users = User::all();
        if ($users->count() < 4) {
            echo "Warning: Need at least 4 users for proper testing. Creating sample users.\n";
            // You would need to create users here if needed
        }

        // Create sample daily reports (last 30 days)
        $weatherConditions = ['صافي', 'غائم', 'ممطر', 'عاصف'];
        
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i);
            $project = $projects[array_rand($projects)];
            
            $weather = $weatherConditions[array_rand($weatherConditions)];
            $temperature = rand(20, 45);
            $humidity = rand(30, 80);
            
            $report = DailyReport::create([
                'project_id' => $project->id,
                'report_number' => DailyReport::generateReportNumber($date->year),
                'report_date' => $date,
                'weather_condition' => $weather,
                'temperature' => $temperature,
                'humidity' => $humidity,
                'site_conditions' => 'ظروف الموقع طبيعية',
                'work_start_time' => '07:00',
                'work_end_time' => '15:00',
                'total_work_hours' => 8,
                'workers_count' => rand(15, 50),
                'workers_breakdown' => json_encode([
                    'مهندسين' => rand(2, 5),
                    'فنيين' => rand(5, 10),
                    'عمال' => rand(10, 35),
                ]),
                'attendance_notes' => 'الحضور كامل',
                'work_executed' => 'تم إنجاز الأعمال المخططة لهذا اليوم بنجاح.',
                'quality_notes' => 'الجودة مطابقة للمواصفات',
                'problems' => $weather === 'ممطر' || $weather === 'عاصف' ? 'تأخر في العمل بسبب الطقس' : null,
                'delays' => $weather === 'ممطر' || $weather === 'عاصف' ? 'تأخير 2 ساعة' : null,
                'general_notes' => 'سير العمل جيد',
                'status' => rand(0, 10) > 3 ? 'submitted' : 'draft',
                'prepared_by' => $users->first()->id,
                'prepared_at' => $date,
            ]);

            // Add signatures for submitted reports
            if ($report->status === 'submitted' && $users->count() >= 3) {
                if (rand(0, 10) > 2) {
                    $report->update([
                        'reviewed_by' => $users->skip(1)->first()->id ?? $users->first()->id,
                        'reviewed_at' => $date->addHours(2),
                    ]);
                }

                if (rand(0, 10) > 4) {
                    $report->update([
                        'consultant_approved_by' => $users->skip(2)->first()->id ?? $users->first()->id,
                        'consultant_approved_at' => $date->addHours(4),
                        'status' => 'approved',
                    ]);
                }
            }
        }

        echo "Daily Reports seeder completed successfully!\n";
        echo "Created:\n";
        echo "- " . count($projects) . " projects\n";
        echo "- " . (count($projects) * count($activityNames)) . " activities\n";
        echo "- 30 daily reports\n";
    }
}
