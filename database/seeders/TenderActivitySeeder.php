<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderWbs;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class TenderActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample units
        $units = [
            ['name' => 'Meter', 'name_en' => 'Meter', 'symbol' => 'm', 'is_active' => true],
            ['name' => 'Square Meter', 'name_en' => 'Square Meter', 'symbol' => 'm²', 'is_active' => true],
            ['name' => 'Cubic Meter', 'name_en' => 'Cubic Meter', 'symbol' => 'm³', 'is_active' => true],
            ['name' => 'Kilogram', 'name_en' => 'Kilogram', 'symbol' => 'kg', 'is_active' => true],
            ['name' => 'Hour', 'name_en' => 'Hour', 'symbol' => 'hr', 'is_active' => true],
            ['name' => 'Day', 'name_en' => 'Day', 'symbol' => 'day', 'is_active' => true],
            ['name' => 'Piece', 'name_en' => 'Piece', 'symbol' => 'pc', 'is_active' => true],
        ];

        foreach ($units as $unitData) {
            Unit::create($unitData);
        }

        // Get or create a company
        $company = Company::first();
        if (!$company) {
            $company = Company::factory()->create();
        }

        // Create sample tender
        $tender = Tender::create([
            'tender_number' => 'TND-2026-001',
            'title' => 'Construction of Office Building',
            'description' => 'Complete construction project including civil, electrical, and mechanical works',
            'company_id' => $company->id,
            'submission_date' => now()->addDays(30),
            'opening_date' => now()->addDays(35),
            'status' => 'open',
            'budget' => 5000000.00,
        ]);

        // Create WBS structure
        $civilWbs = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => 'WBS-001',
            'name' => 'Civil Works',
            'description' => 'All civil construction activities',
            'level' => 1,
            'sequence_order' => 1,
        ]);

        $electricalWbs = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => 'WBS-002',
            'name' => 'Electrical Works',
            'description' => 'All electrical installation activities',
            'level' => 1,
            'sequence_order' => 2,
        ]);

        // Get units
        $meterUnit = Unit::where('symbol', 'm')->first();
        $sqmUnit = Unit::where('symbol', 'm²')->first();
        $cubicMeterUnit = Unit::where('symbol', 'm³')->first();
        $pieceUnit = Unit::where('symbol', 'pc')->first();

        // Create sample activities for Civil Works
        $excavation = TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-001',
            'activity_name' => 'Site Excavation',
            'description' => 'Excavation work for foundation',
            'quantity' => 500.00,
            'unit_id' => $cubicMeterUnit->id,
            'unit_price' => 150.00,
            'wbs_id' => $civilWbs->id,
            'sequence_order' => 1,
            'status' => 'in_progress',
            'start_date' => now(),
            'end_date' => now()->addDays(10),
            'completion_percentage' => 35.50,
            'company_id' => $company->id,
        ]);

        TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-002',
            'activity_name' => 'Foundation Work',
            'description' => 'Concrete foundation construction',
            'quantity' => 200.00,
            'unit_id' => $cubicMeterUnit->id,
            'unit_price' => 800.00,
            'wbs_id' => $civilWbs->id,
            'parent_activity_id' => $excavation->id,
            'sequence_order' => 2,
            'status' => 'pending',
            'start_date' => now()->addDays(11),
            'end_date' => now()->addDays(20),
            'completion_percentage' => 0.00,
            'company_id' => $company->id,
        ]);

        TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-003',
            'activity_name' => 'Wall Construction',
            'description' => 'Brick wall construction for all floors',
            'quantity' => 1500.00,
            'unit_id' => $sqmUnit->id,
            'unit_price' => 250.00,
            'wbs_id' => $civilWbs->id,
            'sequence_order' => 3,
            'status' => 'pending',
            'start_date' => now()->addDays(21),
            'end_date' => now()->addDays(45),
            'completion_percentage' => 0.00,
            'company_id' => $company->id,
        ]);

        // Create sample activities for Electrical Works
        TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-004',
            'activity_name' => 'Electrical Conduit Installation',
            'description' => 'Installation of electrical conduits',
            'quantity' => 2000.00,
            'unit_id' => $meterUnit->id,
            'unit_price' => 45.00,
            'wbs_id' => $electricalWbs->id,
            'sequence_order' => 1,
            'status' => 'pending',
            'start_date' => now()->addDays(25),
            'end_date' => now()->addDays(40),
            'completion_percentage' => 0.00,
            'company_id' => $company->id,
        ]);

        TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-005',
            'activity_name' => 'Lighting Fixtures Installation',
            'description' => 'Installation of LED lighting fixtures',
            'quantity' => 150.00,
            'unit_id' => $pieceUnit->id,
            'unit_price' => 350.00,
            'wbs_id' => $electricalWbs->id,
            'sequence_order' => 2,
            'status' => 'pending',
            'start_date' => now()->addDays(41),
            'end_date' => now()->addDays(50),
            'completion_percentage' => 0.00,
            'company_id' => $company->id,
        ]);
    }
}
