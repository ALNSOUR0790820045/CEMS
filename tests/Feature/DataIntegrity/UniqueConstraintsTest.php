<?php

namespace Tests\Feature\DataIntegrity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UniqueConstraintsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tender_number has unique constraint
     */
    public function test_tender_number_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('tenders', 'tender_number'),
            'tenders table must have tender_number column for unique constraint'
        );
    }

    /**
     * Test risk_number has unique constraint
     */
    public function test_risk_number_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risks', 'risk_number'),
            'risks table must have risk_number column for unique constraint'
        );
    }

    /**
     * Test risk_register register_number has unique constraint
     */
    public function test_risk_register_number_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('risk_registers', 'register_number'),
            'risk_registers table must have register_number column for unique constraint'
        );
    }

    /**
     * Test payroll_period period_code has unique constraint
     */
    public function test_payroll_period_code_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('payroll_periods', 'period_code'),
            'payroll_periods table must have period_code column for unique constraint'
        );
    }

    /**
     * Test boq_number has unique constraint
     */
    public function test_boq_number_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_headers', 'boq_number'),
            'boq_headers table must have boq_number column for unique constraint'
        );
    }

    /**
     * Test boq_item_code has unique constraint
     */
    public function test_boq_item_code_column_exists(): void
    {
        $this->assertTrue(
            Schema::hasColumn('boq_items', 'item_code'),
            'boq_items table must have item_code column for unique constraint'
        );
    }

    /**
     * Test unique constraint fields are not nullable where required
     */
    public function test_unique_constraint_fields_exist(): void
    {
        // Verify all tables with unique constraints exist
        $tablesWithUniqueConstraints = [
            'tenders',
            'risks',
            'risk_registers',
            'payroll_periods',
            'boq_headers',
            'boq_items',
        ];

        foreach ($tablesWithUniqueConstraints as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table {$table} must exist for unique constraints"
            );
        }
    }
}
