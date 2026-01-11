<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PayrollModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test payroll tables exist
     */
    public function test_payroll_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('payroll_periods'));
        $this->assertTrue(Schema::hasTable('payroll_entries'));
        $this->assertTrue(Schema::hasTable('payroll_allowances'));
        $this->assertTrue(Schema::hasTable('payroll_deductions'));
    }

    /**
     * Test payroll_periods has correct columns
     */
    public function test_payroll_periods_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('payroll_periods', [
            'id',
            'period_code',
            'period_name',
            'start_date',
            'end_date',
            'payment_date',
            'status',
            'total_gross',
            'total_deductions',
            'total_net',
            'processed_by',
            'approved_by',
            'company_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test payroll_entries has correct columns
     */
    public function test_payroll_entries_has_correct_columns(): void
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
            'overtime_hours',
            'overtime_amount',
            'status',
            'payment_method',
            'bank_account_id',
            'payment_date',
            'notes',
            'company_id',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test payroll_allowances has correct columns
     */
    public function test_payroll_allowances_has_correct_columns(): void
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
     * Test payroll_deductions has correct columns
     */
    public function test_payroll_deductions_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('payroll_deductions', [
            'id',
            'payroll_entry_id',
            'deduction_type',
            'deduction_name',
            'amount',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test payroll foreign keys
     */
    public function test_payroll_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'payroll_period_id'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'employee_id'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'bank_account_id'));
        $this->assertTrue(Schema::hasColumn('payroll_allowances', 'payroll_entry_id'));
        $this->assertTrue(Schema::hasColumn('payroll_deductions', 'payroll_entry_id'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'company_id'));
        $this->assertTrue(Schema::hasColumn('payroll_periods', 'company_id'));
    }

    /**
     * Test payroll unique constraints
     */
    public function test_payroll_unique_constraints(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_periods', 'period_code'));
    }

    /**
     * Test payroll status enums
     */
    public function test_payroll_status_enums(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_periods', 'status'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'status'));
    }

    /**
     * Test payroll_entries has payment_method enum
     */
    public function test_payroll_entries_has_payment_method(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'payment_method'));
    }

    /**
     * Test payroll_allowances has allowance_type enum
     */
    public function test_payroll_allowances_has_allowance_type(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_allowances', 'allowance_type'));
    }

    /**
     * Test payroll_deductions has deduction_type enum
     */
    public function test_payroll_deductions_has_deduction_type(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_deductions', 'deduction_type'));
    }

    /**
     * Test payroll decimal fields
     */
    public function test_payroll_decimal_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'basic_salary'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'total_allowances'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'total_deductions'));
        $this->assertTrue(Schema::hasColumn('payroll_entries', 'overtime_amount'));
        $this->assertTrue(Schema::hasColumn('payroll_allowances', 'amount'));
        $this->assertTrue(Schema::hasColumn('payroll_deductions', 'amount'));
    }
}
