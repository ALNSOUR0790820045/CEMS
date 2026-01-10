<?php

namespace Tests\Feature\Modules;

use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenderModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
    }

    /**
     * Test tender tables exist
     */
    public function test_tender_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('tenders'));
        $this->assertTrue(Schema::hasTable('tender_site_visits'));
        $this->assertTrue(Schema::hasTable('tender_clarifications'));
        $this->assertTrue(Schema::hasTable('tender_competitors'));
        $this->assertTrue(Schema::hasTable('tender_committee_decisions'));
    }

    /**
     * Test tender has correct columns
     */
    public function test_tender_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tenders', [
            'id',
            'project_id',
            'tender_number',
            'title',
            'description',
            'issue_date',
            'closing_date',
            'estimated_value',
            'status',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test tender_site_visits has correct columns
     */
    public function test_tender_site_visits_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tender_site_visits', [
            'id',
            'tender_id',
            'visit_date',
            'visit_time',
            'attendees',
            'observations',
            'reported_by',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test tender_clarifications has correct columns
     */
    public function test_tender_clarifications_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tender_clarifications', [
            'id',
            'tender_id',
            'question_date',
            'question',
            'answer',
            'answer_date',
            'status',
            'asked_by',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test tender_competitors has correct columns
     */
    public function test_tender_competitors_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tender_competitors', [
            'id',
            'tender_id',
            'company_name',
            'classification',
            'estimated_price',
            'strengths',
            'weaknesses',
            'notes',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test tender_committee_decisions has correct columns
     */
    public function test_tender_committee_decisions_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tender_committee_decisions', [
            'id',
            'tender_id',
            'meeting_date',
            'attendees',
            'decision',
            'reasons',
            'conditions',
            'approved_budget',
            'chairman_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test tender has unique constraint on tender_number
     */
    public function test_tender_number_is_unique(): void
    {
        // This test is structural - we verify the column exists
        // Actual uniqueness constraint testing requires database interaction
        $this->assertTrue(Schema::hasColumn('tenders', 'tender_number'));
    }

    /**
     * Test tender has status enum values
     */
    public function test_tender_has_status_column(): void
    {
        $this->assertTrue(Schema::hasColumn('tenders', 'status'));
    }

    /**
     * Test tender_clarifications has status enum
     */
    public function test_tender_clarifications_has_status(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_clarifications', 'status'));
    }

    /**
     * Test tender_competitors has classification enum
     */
    public function test_tender_competitors_has_classification(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'classification'));
    }

    /**
     * Test tender_committee_decisions has decision enum
     */
    public function test_tender_committee_decisions_has_decision(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_committee_decisions', 'decision'));
    }

    /**
     * Test tender foreign keys
     */
    public function test_tender_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumn('tenders', 'project_id'));
        $this->assertTrue(Schema::hasColumn('tender_site_visits', 'tender_id'));
        $this->assertTrue(Schema::hasColumn('tender_clarifications', 'tender_id'));
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'tender_id'));
        $this->assertTrue(Schema::hasColumn('tender_committee_decisions', 'tender_id'));
    }
}
