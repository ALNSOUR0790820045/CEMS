<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\LaborCategory;
use App\Models\Laborer;
use Illuminate\Support\Facades\Hash;

class LaborManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user if none exists
        $user = User::firstOrCreate(
            ['email' => 'admin@cems.test'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
            ]
        );

        // Create a test company
        $company = Company::firstOrCreate(
            ['slug' => 'test-company'],
            [
                'name' => 'شركة الاختبار',
                'name_en' => 'Test Company',
                'country' => 'SA',
                'is_active' => true,
            ]
        );

        // Create test projects
        $project1 = Project::firstOrCreate(
            ['project_number' => 'PRJ-001'],
            [
                'name' => 'مشروع البناء الأول',
                'name_en' => 'First Construction Project',
                'company_id' => $company->id,
                'start_date' => now(),
                'status' => 'active',
                'budget' => 1000000,
                'is_active' => true,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['project_number' => 'PRJ-002'],
            [
                'name' => 'مشروع البنية التحتية',
                'name_en' => 'Infrastructure Project',
                'company_id' => $company->id,
                'start_date' => now(),
                'status' => 'active',
                'budget' => 2000000,
                'is_active' => true,
            ]
        );

        // Create subcontractor
        $subcontractor = Subcontractor::firstOrCreate(
            ['code' => 'SUB-001'],
            [
                'name' => 'مقاول الباطن الأول',
                'name_en' => 'First Subcontractor',
                'contact_person' => 'أحمد محمد',
                'phone' => '0501234567',
                'is_active' => true,
            ]
        );

        // Create labor categories
        $categories = [
            [
                'code' => 'CAT-001',
                'name' => 'عامل بناء',
                'name_en' => 'Construction Worker',
                'skill_level' => 'skilled',
                'hourly_rate' => 25.00,
                'daily_rate' => 200.00,
                'overtime_multiplier' => 1.5,
            ],
            [
                'code' => 'CAT-002',
                'name' => 'نجار',
                'name_en' => 'Carpenter',
                'skill_level' => 'highly_skilled',
                'hourly_rate' => 35.00,
                'daily_rate' => 280.00,
                'overtime_multiplier' => 1.5,
            ],
            [
                'code' => 'CAT-003',
                'name' => 'حداد',
                'name_en' => 'Blacksmith',
                'skill_level' => 'skilled',
                'hourly_rate' => 30.00,
                'daily_rate' => 240.00,
                'overtime_multiplier' => 1.5,
            ],
        ];

        foreach ($categories as $categoryData) {
            LaborCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }

        // Create laborers
        $laborCategory = LaborCategory::where('code', 'CAT-001')->first();
        
        for ($i = 1; $i <= 5; $i++) {
            Laborer::firstOrCreate(
                ['labor_number' => 'LBR-' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => 'عامل رقم ' . $i,
                    'name_en' => 'Worker ' . $i,
                    'category_id' => $laborCategory->id,
                    'nationality' => 'السعودية',
                    'employment_type' => 'permanent',
                    'joining_date' => now()->subDays(30),
                    'daily_wage' => 200,
                    'overtime_rate' => 25,
                    'status' => $i <= 3 ? 'assigned' : 'available',
                    'current_project_id' => $i <= 3 ? $project1->id : null,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Labor Management test data seeded successfully!');
    }
}
