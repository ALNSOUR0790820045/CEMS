<?php

namespace Tests\Feature\DataIntegrity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NullableFieldsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test nullable fields in tenders table
     */
    public function test_tenders_nullable_fields(): void
    {
        // Verify optional fields exist
        $this->assertTrue(Schema::hasColumn('tenders', 'project_id'));
        $this->assertTrue(Schema::hasColumn('tenders', 'description'));
        $this->assertTrue(Schema::hasColumn('tenders', 'issue_date'));
        $this->assertTrue(Schema::hasColumn('tenders', 'closing_date'));
    }

    /**
     * Test nullable fields in risks table
     */
    public function test_risks_nullable_fields(): void
    {
        // Verify optional fields exist
        $this->assertTrue(Schema::hasColumn('risks', 'source'));
        $this->assertTrue(Schema::hasColumn('risks', 'trigger_events'));
        $this->assertTrue(Schema::hasColumn('risks', 'affected_objectives'));
        $this->assertTrue(Schema::hasColumn('risks', 'response_strategy'));
        $this->assertTrue(Schema::hasColumn('risks', 'response_plan'));
        $this->assertTrue(Schema::hasColumn('risks', 'contingency_plan'));
        $this->assertTrue(Schema::hasColumn('risks', 'owner_id'));
        $this->assertTrue(Schema::hasColumn('risks', 'due_date'));
        $this->assertTrue(Schema::hasColumn('risks', 'closed_date'));
    }

    /**
     * Test nullable fields in risk_registers table
     */
    public function test_risk_registers_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('risk_registers', 'description'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'approved_by_id'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'approved_at'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'last_review_date'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'next_review_date'));
    }

    /**
     * Test nullable fields in tender_site_visits table
     */
    public function test_tender_site_visits_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_site_visits', 'visit_time'));
        $this->assertTrue(Schema::hasColumn('tender_site_visits', 'observations'));
        $this->assertTrue(Schema::hasColumn('tender_site_visits', 'photos'));
        $this->assertTrue(Schema::hasColumn('tender_site_visits', 'coordinates'));
    }

    /**
     * Test nullable fields in tender_clarifications table
     */
    public function test_tender_clarifications_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_clarifications', 'answer'));
        $this->assertTrue(Schema::hasColumn('tender_clarifications', 'answer_date'));
    }

    /**
     * Test nullable fields in boq_items table
     */
    public function test_boq_items_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_items', 'wbs_id'));
    }

    /**
     * Test nullable fields in boq_headers table
     */
    public function test_boq_headers_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'description'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'approved_by'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'approved_at'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'notes'));
    }

    /**
     * Test nullable fields in payroll_entries table
     */
    public function test_payroll_entries_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'bank_account_id'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'payment_date'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'notes'));
    }

    /**
     * Test nullable fields in tender_competitors table
     */
    public function test_tender_competitors_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'estimated_price'));
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'strengths'));
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'weaknesses'));
        $this->assertTrue(Schema::hasColumn('tender_competitors', 'notes'));
    }

    /**
     * Test nullable fields in tender_committee_decisions table
     */
    public function test_tender_committee_decisions_nullable_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('tender_committee_decisions', 'reasons'));
        $this->assertTrue(Schema::hasColumn('tender_committee_decisions', 'conditions'));
        $this->assertTrue(Schema::hasColumn('tender_committee_decisions', 'approved_budget'));
    }
}
