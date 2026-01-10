<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Company;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Note: This seeder requires a company to exist
        // It will create positions for the first company or skip if no companies exist
        $company = Company::first();
        
        if (!$company) {
            $this->command->warn('No companies found. Please create a company first before seeding positions.');
            return;
        }

        $positions = [
            ['name' => 'مدير عام', 'name_en' => 'General Manager', 'description' => 'الإدارة العليا للشركة', 'company_id' => $company->id],
            ['name' => 'مدير مشروع', 'name_en' => 'Project Manager', 'description' => 'إدارة المشاريع', 'company_id' => $company->id],
            ['name' => 'مهندس مدني', 'name_en' => 'Civil Engineer', 'description' => 'الإشراف الهندسي', 'company_id' => $company->id],
            ['name' => 'محاسب', 'name_en' => 'Accountant', 'description' => 'المحاسبة والشؤون المالية', 'company_id' => $company->id],
            ['name' => 'مسؤول موارد بشرية', 'name_en' => 'HR Officer', 'description' => 'إدارة الموظفين', 'company_id' => $company->id],
            ['name' => 'مشرف موقع', 'name_en' => 'Site Supervisor', 'description' => 'الإشراف على الموقع', 'company_id' => $company->id],
            ['name' => 'فني', 'name_en' => 'Technician', 'description' => 'الأعمال الفنية', 'company_id' => $company->id],
            ['name' => 'عامل', 'name_en' => 'Worker', 'description' => 'العمالة', 'company_id' => $company->id],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
        
        $this->command->info('Positions seeded successfully for company: ' . $company->name);
    }
}
