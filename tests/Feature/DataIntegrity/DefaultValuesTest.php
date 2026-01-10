<?php

namespace Tests\Feature\DataIntegrity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DefaultValuesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tenders table has status default value
     */
    public function test_tenders_has_status_column(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tenders', 'status'),
            'tenders must have status column with default value'
        );
    }

    /**
     * Test risks table has status default value
     */
    public function test_risks_has_status_column(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risks', 'status'),
            'risks must have status column with default value'
        );
    }

    /**
     * Test risk_registers table has status and version defaults
     */
    public function test_risk_registers_has_default_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('risk_registers', 'status'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'version'));
        $this->assertTrue(Schema::hasColumn('risk_registers', 'review_frequency'));
    }

    /**
     * Test tender_clarifications has status default
     */
    public function test_tender_clarifications_has_status_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tender_clarifications', 'status'),
            'tender_clarifications must have status with default value'
        );
    }

    /**
     * Test tender_committee_decisions has decision default
     */
    public function test_tender_committee_decisions_has_decision_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tender_committee_decisions', 'decision'),
            'tender_committee_decisions must have decision with default value'
        );
    }

    /**
     * Test tender_competitors has classification default
     */
    public function test_tender_competitors_has_classification_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tender_competitors', 'classification'),
            'tender_competitors must have classification with default value'
        );
    }

    /**
     * Test boq_items has default values for numeric fields
     */
    public function test_boq_items_has_numeric_defaults(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_items', 'quantity'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'unit_price'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'total_price'));
        $this->assertTrue(Schema::hasColumn('boq_items', 'sort_order'));
    }

    /**
     * Test boq_headers has default values
     */
    public function test_boq_headers_has_defaults(): void
    {
        $this->assertTrue(Schema::hasColumn('boq_headers', 'status'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'type'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'version'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'currency'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'total_amount'));
        $this->assertTrue(Schema::hasColumn('boq_headers', 'final_amount'));
    }

    /**
     * Test payroll_entries has default values
     */
    public function test_payroll_entries_has_defaults(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'total_allowances'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'total_deductions'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'days_absent'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'overtime_hours'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'overtime_amount'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'status'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'payment_method'));
    }

    /**
     * Test payroll_allowances has default values
     */
    public function test_payroll_allowances_has_defaults(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_allowances', 'allowance_type'));
        $this->assertTrue(Schema::hasColumn('payroll_allowances', 'is_taxable'));
    }

    /**
     * Test boq_item_resources has default wastage
     */
    public function test_boq_item_resources_has_wastage_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_item_resources', 'wastage_percentage'),
            'boq_item_resources must have wastage_percentage with default value'
        );
    }

    /**
     * Test boq_sections has default sort_order
     */
    public function test_boq_sections_has_sort_order_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_sections', 'sort_order'),
            'boq_sections must have sort_order with default value'
        );
    }
}
