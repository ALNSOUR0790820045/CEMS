<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderWbs;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenderActivityMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_units_table_exists_with_correct_columns(): void
    {
        $this->assertTrue(Schema::hasTable('units'));
        $this->assertTrue(Schema::hasColumns('units', [
            'id', 'name', 'name_en', 'symbol', 'description', 'is_active', 'created_at', 'updated_at'
        ]));
    }

    public function test_tenders_table_exists_with_correct_columns(): void
    {
        $this->assertTrue(Schema::hasTable('tenders'));
        $this->assertTrue(Schema::hasColumns('tenders', [
            'id', 'tender_number', 'title', 'description', 'company_id',
            'submission_date', 'opening_date', 'status', 'budget', 'created_at', 'updated_at'
        ]));
    }

    public function test_tender_wbs_table_exists_with_correct_columns(): void
    {
        $this->assertTrue(Schema::hasTable('tender_wbs'));
        $this->assertTrue(Schema::hasColumns('tender_wbs', [
            'id', 'tender_id', 'wbs_code', 'name', 'description',
            'parent_wbs_id', 'level', 'sequence_order', 'created_at', 'updated_at'
        ]));
    }

    public function test_tender_activities_table_exists_with_correct_columns(): void
    {
        $this->assertTrue(Schema::hasTable('tender_activities'));
        $this->assertTrue(Schema::hasColumns('tender_activities', [
            'id', 'tender_id', 'activity_code', 'activity_name', 'description',
            'quantity', 'unit_id', 'unit_price', 'total_amount', 'wbs_id',
            'parent_activity_id', 'sequence_order', 'status', 'start_date',
            'end_date', 'completion_percentage', 'company_id', 'created_at', 'updated_at'
        ]));
    }

    public function test_tender_activities_has_required_indexes(): void
    {
        $indexes = Schema::getIndexes('tender_activities');
        $indexNames = array_column($indexes, 'name');
        
        $this->assertContains('idx_tender_activities_tender', $indexNames);
        $this->assertContains('idx_tender_activities_wbs', $indexNames);
        $this->assertContains('idx_tender_activities_status', $indexNames);
    }

    public function test_can_create_tender_activity_with_relationships(): void
    {
        $company = Company::factory()->create();
        
        $unit = Unit::create([
            'name' => 'Meter',
            'symbol' => 'm',
            'is_active' => true,
        ]);

        $tender = Tender::create([
            'tender_number' => 'T-001',
            'title' => 'Test Tender',
            'company_id' => $company->id,
            'status' => 'draft',
        ]);

        $wbs = TenderWbs::create([
            'tender_id' => $tender->id,
            'wbs_code' => 'WBS-001',
            'name' => 'Test WBS',
            'level' => 1,
        ]);

        $activity = TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-001',
            'activity_name' => 'Test Activity',
            'quantity' => 10.00,
            'unit_id' => $unit->id,
            'unit_price' => 100.00,
            'wbs_id' => $wbs->id,
            'status' => 'pending',
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('tender_activities', [
            'activity_code' => 'ACT-001',
            'activity_name' => 'Test Activity',
        ]);

        $this->assertEquals(1000.00, $activity->total_amount);
        $this->assertEquals($tender->id, $activity->tender->id);
        $this->assertEquals($unit->id, $activity->unit->id);
        $this->assertEquals($wbs->id, $activity->wbs->id);
        $this->assertEquals($company->id, $activity->company->id);
    }

    public function test_tender_activity_status_enum_values(): void
    {
        $company = Company::factory()->create();
        
        $tender = Tender::create([
            'tender_number' => 'T-002',
            'title' => 'Test Tender 2',
            'company_id' => $company->id,
            'status' => 'draft',
        ]);

        foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status) {
            $activity = TenderActivity::create([
                'tender_id' => $tender->id,
                'activity_code' => 'ACT-' . $status,
                'activity_name' => 'Activity ' . $status,
                'quantity' => 1.00,
                'unit_price' => 100.00,
                'status' => $status,
                'company_id' => $company->id,
            ]);

            $this->assertEquals($status, $activity->fresh()->status);
        }
    }

    public function test_tender_activity_self_referential_relationship(): void
    {
        $company = Company::factory()->create();
        
        $tender = Tender::create([
            'tender_number' => 'T-003',
            'title' => 'Test Tender 3',
            'company_id' => $company->id,
            'status' => 'draft',
        ]);

        $parentActivity = TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-PARENT',
            'activity_name' => 'Parent Activity',
            'quantity' => 1.00,
            'unit_price' => 100.00,
            'status' => 'pending',
            'company_id' => $company->id,
        ]);

        $childActivity = TenderActivity::create([
            'tender_id' => $tender->id,
            'activity_code' => 'ACT-CHILD',
            'activity_name' => 'Child Activity',
            'quantity' => 1.00,
            'unit_price' => 50.00,
            'parent_activity_id' => $parentActivity->id,
            'status' => 'pending',
            'company_id' => $company->id,
        ]);

        $this->assertEquals($parentActivity->id, $childActivity->parent->id);
        $this->assertTrue($parentActivity->children->contains($childActivity));
    }
}
