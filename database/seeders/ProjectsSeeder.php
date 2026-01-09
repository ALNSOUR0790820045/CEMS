<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0501234567',
            'job_title' => 'مدير عام',
            'employee_id' => 'EMP-001',
            'is_active' => true,
            'language' => 'ar',
        ]);

        // Create test clients
        $client1 = Client::create([
            'name' => 'وزارة الإسكان',
            'name_en' => 'Ministry of Housing',
            'email' => 'housing@gov.sa',
            'phone' => '0112345678',
            'address' => 'الرياض، طريق الملك فهد',
            'city' => 'الرياض',
            'country' => 'Saudi Arabia',
            'type' => 'government',
            'is_active' => true,
        ]);

        $client2 = Client::create([
            'name' => 'شركة التطوير العقاري',
            'name_en' => 'Real Estate Development Company',
            'email' => 'info@realestate.sa',
            'phone' => '0123456789',
            'address' => 'جدة، حي الروضة',
            'city' => 'جدة',
            'country' => 'Saudi Arabia',
            'type' => 'private',
            'is_active' => true,
        ]);

        // Create test projects
        Project::create([
            'project_number' => 'PRJ-2026-0001',
            'name' => 'مشروع مجمع سكني الربوة',
            'name_en' => 'Rabwa Residential Complex Project',
            'description' => 'مشروع مجمع سكني متكامل يتضمن 200 وحدة سكنية',
            'client_id' => $client1->id,
            'type' => 'building',
            'category' => 'new_construction',
            'location' => 'شمال الرياض، حي الربوة',
            'city' => 'الرياض',
            'region' => 'المنطقة الوسطى',
            'country' => 'Saudi Arabia',
            'commencement_date' => '2026-01-01',
            'original_completion_date' => '2026-12-31',
            'original_duration_days' => 365,
            'original_contract_value' => 50000000,
            'revised_contract_value' => 50000000,
            'currency' => 'SAR',
            'physical_progress' => 25.5,
            'status' => 'in_progress',
            'health' => 'on_track',
            'priority' => 'high',
            'project_manager_id' => $user->id,
            'site_engineer_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Project::create([
            'project_number' => 'PRJ-2026-0002',
            'name' => 'مشروع برج الأعمال التجاري',
            'name_en' => 'Business Tower Project',
            'description' => 'برج تجاري بارتفاع 30 طابق',
            'client_id' => $client2->id,
            'type' => 'building',
            'category' => 'new_construction',
            'location' => 'طريق الملك عبدالله، جدة',
            'city' => 'جدة',
            'region' => 'المنطقة الغربية',
            'country' => 'Saudi Arabia',
            'commencement_date' => '2025-06-01',
            'original_completion_date' => '2027-06-01',
            'original_duration_days' => 730,
            'original_contract_value' => 120000000,
            'revised_contract_value' => 125000000,
            'approved_variations' => 5000000,
            'currency' => 'SAR',
            'physical_progress' => 45.0,
            'status' => 'in_progress',
            'health' => 'at_risk',
            'priority' => 'critical',
            'project_manager_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Project::create([
            'project_number' => 'PRJ-2026-0003',
            'name' => 'مشروع طريق الدائري الشرقي',
            'name_en' => 'Eastern Ring Road Project',
            'description' => 'تطوير وتوسعة الدائري الشرقي',
            'client_id' => $client1->id,
            'type' => 'infrastructure',
            'category' => 'expansion',
            'location' => 'الدائري الشرقي',
            'city' => 'الرياض',
            'region' => 'المنطقة الوسطى',
            'country' => 'Saudi Arabia',
            'commencement_date' => '2025-01-01',
            'original_completion_date' => '2026-06-30',
            'original_duration_days' => 545,
            'original_contract_value' => 85000000,
            'revised_contract_value' => 85000000,
            'currency' => 'SAR',
            'physical_progress' => 78.5,
            'status' => 'in_progress',
            'health' => 'on_track',
            'priority' => 'medium',
            'project_manager_id' => $user->id,
            'created_by' => $user->id,
        ]);

        Project::create([
            'project_number' => 'PRJ-2025-0015',
            'name' => 'مشروع مصنع الإسمنت',
            'name_en' => 'Cement Factory Project',
            'description' => 'إنشاء مصنع إسمنت بطاقة إنتاجية 5000 طن يومياً',
            'client_id' => $client2->id,
            'type' => 'industrial',
            'category' => 'new_construction',
            'location' => 'المنطقة الصناعية، ينبع',
            'city' => 'ينبع',
            'region' => 'المنطقة الغربية',
            'country' => 'Saudi Arabia',
            'commencement_date' => '2024-03-01',
            'original_completion_date' => '2025-12-31',
            'actual_completion_date' => '2025-12-15',
            'original_duration_days' => 670,
            'original_contract_value' => 200000000,
            'revised_contract_value' => 210000000,
            'approved_variations' => 10000000,
            'currency' => 'SAR',
            'physical_progress' => 100.0,
            'status' => 'completed',
            'health' => 'on_track',
            'priority' => 'high',
            'project_manager_id' => $user->id,
            'created_by' => $user->id,
        ]);
    }
}
