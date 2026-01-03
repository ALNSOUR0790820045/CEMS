<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Note: This seeder requires a company to exist
        // It will create departments for the first company or skip if no companies exist
        $company = Company::first();
        
        if (!$company) {
            $this->command->warn('No companies found. Please create a company first before seeding departments.');
            return;
        }

        $departments = [
            ['name' => 'الموارد البشرية', 'name_en' => 'Human Resources', 'description' => 'إدارة شؤون الموظفين والتوظيف', 'company_id' => $company->id],
            ['name' => 'المالية', 'name_en' => 'Finance', 'description' => 'إدارة الحسابات والمالية', 'company_id' => $company->id],
            ['name' => 'المشاريع', 'name_en' => 'Projects', 'description' => 'إدارة وتنفيذ المشاريع', 'company_id' => $company->id],
            ['name' => 'الهندسة', 'name_en' => 'Engineering', 'description' => 'الإشراف الهندسي والتصميم', 'company_id' => $company->id],
            ['name' => 'المشتريات', 'name_en' => 'Procurement', 'description' => 'إدارة المشتريات والتوريد', 'company_id' => $company->id],
            ['name' => 'تكنولوجيا المعلومات', 'name_en' => 'IT', 'description' => 'الدعم التقني وإدارة الأنظمة', 'company_id' => $company->id],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
        
        $this->command->info('Departments seeded successfully for company: ' . $company->name);
    }
}
