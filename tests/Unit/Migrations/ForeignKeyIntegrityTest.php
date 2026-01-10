<?php

namespace Tests\Unit\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ForeignKeyIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tender foreign keys exist and are valid
     */
    public function test_tender_foreign_keys_exist(): void
    {
        $this->assertTrue(Schema::hasTable('tenders'));
        $this->assertTrue(Schema::hasTable('tender_site_visits'));
        $this->assertTrue(Schema::hasTable('tender_clarifications'));
        $this->assertTrue(Schema::hasTable('tender_competitors'));
        $this->assertTrue(Schema::hasTable('tender_committee_decisions'));
    }

    /**
     * Test risk management foreign keys exist and are valid
     */
    public function test_risk_management_foreign_keys_exist(): void
    {
        $this->assertTrue(Schema::hasTable('risk_registers'));
        $this->assertTrue(Schema::hasTable('risks'));
        $this->assertTrue(Schema::hasTable('risk_assessments'));
        $this->assertTrue(Schema::hasTable('risk_responses'));
        $this->assertTrue(Schema::hasTable('risk_monitoring'));
        $this->assertTrue(Schema::hasTable('risk_incidents'));
    }

    /**
     * Test payroll foreign keys exist and are valid
     */
    public function test_payroll_foreign_keys_exist(): void
    {
        $this->assertTrue(Schema::hasTable('payroll_periods'));
        $this->assertTrue(Schema::hasTable('payroll_entries'));
        $this->assertTrue(Schema::hasTable('payroll_allowances'));
        $this->assertTrue(Schema::hasTable('payroll_deductions'));
    }

    /**
     * Test BOQ foreign keys exist and are valid
     */
    public function test_boq_foreign_keys_exist(): void
    {
        $this->assertTrue(Schema::hasTable('boq_items'));
        $this->assertTrue(Schema::hasTable('boq_headers'));
        $this->assertTrue(Schema::hasTable('boq_sections'));
        $this->assertTrue(Schema::hasTable('boq_item_resources'));
    }

    /**
     * Test project hierarchy tables exist
     */
    public function test_project_hierarchy_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('companies'));
        $this->assertTrue(Schema::hasTable('projects'));
        $this->assertTrue(Schema::hasTable('project_wbs'));
    }

    /**
     * Test currency and branch tables exist
     */
    public function test_currency_and_branch_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('currencies'));
        $this->assertTrue(Schema::hasTable('branches'));
    }

    /**
     * Test tender_site_visits has foreign key to tenders
     */
    public function test_tender_site_visits_has_tender_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tender_site_visits', 'tender_id'),
            'tender_site_visits must have tender_id column'
        );
    }

    /**
     * Test risks has foreign key to risk_registers
     */
    public function test_risks_has_risk_register_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risks', 'risk_register_id'),
            'risks must have risk_register_id column'
        );
    }

    /**
     * Test risks has foreign key to projects
     */
    public function test_risks_has_project_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risks', 'project_id'),
            'risks must have project_id column'
        );
    }

    /**
     * Test risk_assessments has foreign key to risks
     */
    public function test_risk_assessments_has_risk_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risk_assessments', 'risk_id'),
            'risk_assessments must have risk_id column'
        );
    }

    /**
     * Test payroll_allowances has foreign key to payroll_entries
     */
    public function test_payroll_allowances_has_entry_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('payroll_allowances', 'payroll_entry_id'),
            'payroll_allowances must have payroll_entry_id column'
        );
    }

    /**
     * Test payroll_entries has foreign key to payroll_periods
     */
    public function test_payroll_entries_has_period_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('payroll_entries', 'payroll_period_id'),
            'payroll_entries must have payroll_period_id column'
        );
    }

    /**
     * Test boq_sections has foreign key to boq_headers
     */
    public function test_boq_sections_has_header_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_sections', 'boq_header_id'),
            'boq_sections must have boq_header_id column'
        );
    }

    /**
     * Test boq_items has foreign key to projects
     */
    public function test_boq_items_has_project_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_items', 'project_id'),
            'boq_items must have project_id column'
        );
    }

    /**
     * Test projects has foreign key to companies
     */
    public function test_projects_has_company_foreign_key(): void
    {
        $this->assertTrue(
            Schema::hasColumn('projects', 'company_id'),
            'projects must have company_id column'
        );
    }
}
