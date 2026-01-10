<?php

namespace Tests\Unit\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TableStructureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tenders table has correct columns
     */
    public function test_tenders_table_structure(): void
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
     * Test risks table has correct columns
     */
    public function test_risks_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('risks', [
            'id',
            'risk_number',
            'risk_register_id',
            'project_id',
            'title',
            'description',
            'category',
            'probability',
            'probability_score',
            'impact',
            'impact_score',
            'risk_score',
            'risk_level',
            'status',
            'company_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test risk_registers table has correct columns
     */
    public function test_risk_registers_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('risk_registers', [
            'id',
            'register_number',
            'project_id',
            'name',
            'description',
            'version',
            'status',
            'prepared_by_id',
            'company_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test payroll_entries table has correct columns
     */
    public function test_payroll_entries_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('payroll_entries', [
            'id',
            'payroll_period_id',
            'employee_id',
            'basic_salary',
            'total_allowances',
            'total_deductions',
            'days_worked',
            'days_absent',
            'status',
            'company_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test payroll_allowances table has correct columns
     */
    public function test_payroll_allowances_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('payroll_allowances', [
            'id',
            'payroll_entry_id',
            'allowance_type',
            'allowance_name',
            'amount',
            'is_taxable',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test boq_headers table has correct columns
     */
    public function test_boq_headers_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_headers', [
            'id',
            'boq_number',
            'name',
            'description',
            'boqable_type',
            'boqable_id',
            'type',
            'status',
            'version',
            'currency',
            'total_amount',
            'final_amount',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test boq_sections table has correct columns
     */
    public function test_boq_sections_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('boq_sections', [
            'id',
            'boq_header_id',
            'code',
            'name',
            'name_en',
            'description',
            'sort_order',
            'total_amount',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test companies table has correct columns
     */
    public function test_companies_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('companies', [
            'id',
            'name',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test projects table has correct columns
     */
    public function test_projects_table_structure(): void
    {
        $this->assertTrue(Schema::hasColumns('projects', [
            'id',
            'company_id',
            'name',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test currencies table exists and has basic structure
     */
    public function test_currencies_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('currencies'));
        $this->assertTrue(Schema::hasColumn('currencies', 'id'));
        $this->assertTrue(Schema::hasColumn('currencies', 'code'));
    }

    /**
     * Test branches table exists and has basic structure
     */
    public function test_branches_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('branches'));
        $this->assertTrue(Schema::hasColumn('branches', 'id'));
        $this->assertTrue(Schema::hasColumn('branches', 'name'));
    }
}
