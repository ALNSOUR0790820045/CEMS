<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first company or create one if none exists
        $company = Company::first();
        
        if (!$company) {
            $company = Company::create([
                'name' => 'شركة الأمثلة',
                'name_en' => 'Example Company',
                'slug' => 'example-company',
                'email' => 'info@example.com',
                'phone' => '+962791234567',
                'address' => 'عمان، الأردن',
                'city' => 'عمان',
                'country' => 'JO',
                'commercial_registration' => 'CR123456',
                'tax_number' => 'TAX123456',
                'is_active' => true,
            ]);
        }

        // Get first user for manager assignment
        $manager = User::first();

        // Create headquarters branch
        Branch::create([
            'company_id' => $company->id,
            'code' => 'HQ-001',
            'name' => 'المقر الرئيسي',
            'name_en' => 'Headquarters',
            'region' => 'العاصمة',
            'city' => 'عمان',
            'country' => 'JO',
            'address' => 'شارع المدينة المنورة، عمان',
            'phone' => '+962791234567',
            'email' => 'hq@example.com',
            'manager_id' => $manager?->id,
            'is_active' => true,
            'is_headquarters' => true,
        ]);

        // Create additional branches
        Branch::create([
            'company_id' => $company->id,
            'code' => 'AMM-002',
            'name' => 'فرع عمان الغربي',
            'name_en' => 'West Amman Branch',
            'region' => 'العاصمة',
            'city' => 'عمان',
            'country' => 'JO',
            'address' => 'شارع الجامعة، عمان',
            'phone' => '+962791234568',
            'email' => 'west-amman@example.com',
            'manager_id' => $manager?->id,
            'is_active' => true,
            'is_headquarters' => false,
        ]);

        Branch::create([
            'company_id' => $company->id,
            'code' => 'IRB-003',
            'name' => 'فرع إربد',
            'name_en' => 'Irbid Branch',
            'region' => 'الشمال',
            'city' => 'إربد',
            'country' => 'JO',
            'address' => 'وسط البلد، إربد',
            'phone' => '+962791234569',
            'email' => 'irbid@example.com',
            'is_active' => true,
            'is_headquarters' => false,
        ]);

        Branch::create([
            'company_id' => $company->id,
            'code' => 'AQB-004',
            'name' => 'فرع العقبة',
            'name_en' => 'Aqaba Branch',
            'region' => 'الجنوب',
            'city' => 'العقبة',
            'country' => 'JO',
            'address' => 'الكورنيش، العقبة',
            'phone' => '+962791234570',
            'email' => 'aqaba@example.com',
            'is_active' => true,
            'is_headquarters' => false,
        ]);

        Branch::create([
            'company_id' => $company->id,
            'code' => 'ZRQ-005',
            'name' => 'فرع الزرقاء',
            'name_en' => 'Zarqa Branch',
            'region' => 'الوسط',
            'city' => 'الزرقاء',
            'country' => 'JO',
            'address' => 'شارع الملك حسين، الزرقاء',
            'phone' => '+962791234571',
            'email' => 'zarqa@example.com',
            'is_active' => false,
        ]);
    }
}
