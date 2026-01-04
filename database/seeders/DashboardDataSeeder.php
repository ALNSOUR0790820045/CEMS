<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\FinancialTransaction;
use App\Models\Inventory;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create first company and user
        $company = Company::first();
        $user = User::first();

        if (!$company) {
            $this->command->warn('No company found. Creating a sample company...');
            $company = Company::create([
                'name' => 'شركة المقاولات النموذجية',
                'name_en' => 'Sample Construction Company',
                'slug' => 'sample-construction-company',
                'email' => 'info@sampleco.com',
                'phone' => '+966123456789',
                'country' => 'SA',
                'is_active' => true,
            ]);
            $this->command->info('Sample company created successfully.');
        }

        if (!$user) {
            $this->command->warn('No user found. Creating a sample user...');
            $user = User::create([
                'name' => 'مستخدم تجريبي',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
            ]);
            $this->command->info('Sample user created successfully.');
        }

        // Seed Projects
        $projects = [
            [
                'name' => 'مشروع برج الأعمال',
                'code' => 'PRJ-2024-001',
                'company_id' => $company->id,
                'status' => 'active',
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
                'planned_value' => 5000000,
                'earned_value' => 3200000,
                'actual_cost' => 3000000,
                'budget' => 5500000,
                'progress' => 65,
                'client_name' => 'شركة التطوير العقاري',
                'location' => 'الرياض',
            ],
            [
                'name' => 'مشروع الطريق السريع',
                'code' => 'PRJ-2024-002',
                'company_id' => $company->id,
                'status' => 'active',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'planned_value' => 8000000,
                'earned_value' => 2400000,
                'actual_cost' => 2600000,
                'budget' => 8500000,
                'progress' => 30,
                'client_name' => 'وزارة النقل',
                'location' => 'جدة',
            ],
            [
                'name' => 'مشروع المجمع السكني',
                'code' => 'PRJ-2023-015',
                'company_id' => $company->id,
                'status' => 'completed',
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->subMonths(1),
                'planned_value' => 3000000,
                'earned_value' => 3000000,
                'actual_cost' => 2900000,
                'budget' => 3200000,
                'progress' => 100,
                'client_name' => 'مؤسسة الإسكان',
                'location' => 'الدمام',
            ],
            [
                'name' => 'مشروع المركز التجاري',
                'code' => 'PRJ-2024-003',
                'company_id' => $company->id,
                'status' => 'delayed',
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->addMonths(8),
                'planned_value' => 6000000,
                'earned_value' => 1500000,
                'actual_cost' => 1800000,
                'budget' => 6200000,
                'progress' => 20,
                'client_name' => 'مجموعة العقارات',
                'location' => 'مكة',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        $projectIds = Project::pluck('id')->toArray();

        // Seed Financial Transactions
        $categories = ['revenue', 'materials', 'labor', 'equipment', 'overhead'];
        for ($i = 0; $i < 50; $i++) {
            $type = $i % 3 === 0 ? 'income' : 'expense';
            FinancialTransaction::create([
                'company_id' => $company->id,
                'project_id' => $projectIds[array_rand($projectIds)],
                'type' => $type,
                'category' => $categories[array_rand($categories)],
                'amount' => rand(10000, 500000),
                'date' => Carbon::now()->subDays(rand(1, 90)),
                'description' => $type === 'income' ? 'دفعة عميل' : 'مصروفات مشروع',
                'reference' => 'REF-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'status' => 'completed',
            ]);
        }

        // Seed Inventory
        $items = [
            ['item_name' => 'اسمنت', 'category' => 'مواد بناء', 'unit' => 'كيس', 'unit_price' => 25],
            ['item_name' => 'حديد تسليح', 'category' => 'مواد بناء', 'unit' => 'طن', 'unit_price' => 2500],
            ['item_name' => 'رمل', 'category' => 'مواد بناء', 'unit' => 'متر مكعب', 'unit_price' => 50],
            ['item_name' => 'طوب', 'category' => 'مواد بناء', 'unit' => 'الف', 'unit_price' => 300],
            ['item_name' => 'بلاط', 'category' => 'تشطيبات', 'unit' => 'متر مربع', 'unit_price' => 45],
        ];

        foreach ($items as $item) {
            $quantity = rand(100, 1000);
            Inventory::create([
                'company_id' => $company->id,
                'project_id' => $projectIds[array_rand($projectIds)],
                'item_name' => $item['item_name'],
                'category' => $item['category'],
                'quantity' => $quantity,
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'total_value' => $quantity * $item['unit_price'],
                'location' => 'مستودع رئيسي',
            ]);
        }

        // Seed Attendance for last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            $checkIn = $date->copy()->setTime(8, 0);
            $checkOut = $date->copy()->setTime(17, 0);
            
            Attendance::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'date' => $date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'present',
                'hours_worked' => 9,
            ]);
        }

        $this->command->info('Dashboard data seeded successfully!');
    }
}
