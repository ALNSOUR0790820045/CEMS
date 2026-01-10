<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tender;
use App\Models\Country;
use App\Models\City;
use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;

class TenderSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('code', 'SA')->first();
        $city = City::where('name', 'الرياض')->first();
        $currency = Currency::where('code', 'SAR')->first();
        $user = User::first();

        if (!$country || !$currency || !$user) {
            $this->command->warn('Please seed countries, currencies, and users first.');
            return;
        }

        $tenders = [
            [
                'tender_name' => 'بناء مجمع سكني شمال الرياض',
                'tender_name_en' => 'Construction of Residential Complex North Riyadh',
                'description' => 'مشروع إنشاء مجمع سكني متكامل يتضمن 200 وحدة سكنية بمساحة إجمالية 50,000 متر مربع. يشمل المشروع البنية التحتية الكاملة وجميع المرافق.',
                'description_en' => 'Construction project of a complete residential complex including 200 housing units with a total area of 50,000 square meters. The project includes complete infrastructure and all facilities.',
                'owner_name' => 'وزارة الإسكان',
                'owner_contact' => 'إدارة المشاريع',
                'owner_email' => 'projects@housing.gov.sa',
                'owner_phone' => '+966112345678',
                'reference_number' => 'HSG-2026-001',
                'country_id' => $country->id,
                'city_id' => $city->id,
                'project_location' => 'شمال الرياض، طريق الملك خالد',
                'tender_type' => 'buildings',
                'contract_type' => 'lump_sum',
                'estimated_value' => 45000000.00,
                'currency_id' => $currency->id,
                'estimated_duration_months' => 24,
                'announcement_date' => Carbon::now()->subDays(10),
                'document_sale_start' => Carbon::now()->subDays(8),
                'document_sale_end' => Carbon::now()->addDays(15),
                'document_price' => 5000.00,
                'site_visit_date' => Carbon::now()->addDays(5),
                'site_visit_time' => '10:00:00',
                'questions_deadline' => Carbon::now()->addDays(18),
                'submission_deadline' => Carbon::now()->addDays(25),
                'submission_time' => '14:00:00',
                'opening_date' => Carbon::now()->addDays(26),
                'opening_time' => '10:00:00',
                'requires_bid_bond' => true,
                'bid_bond_percentage' => 1.00,
                'bid_bond_amount' => 450000.00,
                'bid_bond_validity_days' => 90,
                'eligibility_criteria' => '- تصنيف درجة أولى في المباني
- خبرة لا تقل عن 10 سنوات في المشاريع السكنية
- القدرة المالية لا تقل عن 50 مليون ريال
- مشاريع سابقة مماثلة',
                'status' => 'announced',
                'assigned_to' => $user->id,
            ],
            [
                'tender_name' => 'إنشاء جسر طريق الملك فهد',
                'tender_name_en' => 'Construction of King Fahd Road Bridge',
                'description' => 'مشروع إنشاء جسر على طريق الملك فهد بطول 800 متر وعرض 4 مسارات. يشمل المشروع أعمال الأساسات والإنشاءات الخرسانية والتشطيبات.',
                'owner_name' => 'وزارة النقل والخدمات اللوجستية',
                'owner_contact' => 'إدارة الطرق',
                'owner_email' => 'roads@mot.gov.sa',
                'owner_phone' => '+966118765432',
                'reference_number' => 'TRN-2026-015',
                'country_id' => $country->id,
                'city_id' => $city->id,
                'project_location' => 'طريق الملك فهد، تقاطع شارع العليا',
                'tender_type' => 'bridges',
                'contract_type' => 'unit_price',
                'estimated_value' => 32000000.00,
                'currency_id' => $currency->id,
                'estimated_duration_months' => 18,
                'announcement_date' => Carbon::now()->subDays(5),
                'submission_deadline' => Carbon::now()->addDays(20),
                'requires_bid_bond' => true,
                'bid_bond_percentage' => 1.50,
                'bid_bond_amount' => 480000.00,
                'bid_bond_validity_days' => 120,
                'status' => 'announced',
                'assigned_to' => $user->id,
            ],
            [
                'tender_name' => 'مشروع محطة معالجة مياه الصرف الصحي',
                'tender_name_en' => 'Wastewater Treatment Plant Project',
                'description' => 'إنشاء محطة معالجة مياه صرف صحي بطاقة استيعابية 50,000 متر مكعب يومياً. تشمل جميع المعدات والأنظمة الميكانيكية والكهربائية.',
                'owner_name' => 'المياه الوطنية',
                'owner_email' => 'tenders@nwc.com.sa',
                'reference_number' => 'WTR-2026-008',
                'country_id' => $country->id,
                'city_id' => $city->id,
                'tender_type' => 'water',
                'contract_type' => 'epc',
                'estimated_value' => 85000000.00,
                'currency_id' => $currency->id,
                'estimated_duration_months' => 30,
                'announcement_date' => Carbon::now()->subDays(15),
                'submission_deadline' => Carbon::now()->addDays(10),
                'requires_bid_bond' => true,
                'bid_bond_percentage' => 2.00,
                'bid_bond_amount' => 1700000.00,
                'status' => 'evaluating',
                'participate' => true,
                'participation_decision_notes' => 'نوصي بالمشاركة: لدينا خبرة قوية في محطات المعالجة، والمشروع يتوافق مع استراتيجيتنا',
                'decided_by' => $user->id,
                'decision_date' => Carbon::now()->subDays(3),
                'assigned_to' => $user->id,
            ],
        ];

        foreach ($tenders as $tenderData) {
            Tender::create($tenderData);
        }
    }
}
