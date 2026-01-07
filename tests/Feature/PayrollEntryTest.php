<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company
        $this->company = Company::factory()->create();

        // Create users
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->employee = User::factory()->create([
            'company_id' => $this->company->id,
            'employee_id' => 'EMP001',
        ]);

        // Create a payroll period
        $this->period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'open',
        ]);

        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_payroll_entry(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'user_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson('/api/payroll-entries', [
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'basic_salary' => 5000.00,
            'days_worked' => 30,
            'days_absent' => 0,
            'overtime_hours' => 5.0,
            'overtime_amount' => 250.00,
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $bankAccount->id,
            'allowances' => [
                [
                    'allowance_type' => 'housing',
                    'allowance_name' => 'Housing Allowance',
                    'amount' => 1000.00,
                    'is_taxable' => true,
                ],
            ],
            'deductions' => [
                [
                    'deduction_type' => 'tax',
                    'deduction_name' => 'Income Tax',
                    'amount' => 500.00,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'employee_id',
                    'basic_salary',
                    'total_allowances',
                    'total_deductions',
                ],
            ]);

        $this->assertDatabaseHas('payroll_entries', [
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'basic_salary' => 5000.00,
        ]);

        $this->assertDatabaseHas('payroll_allowances', [
            'allowance_name' => 'Housing Allowance',
            'amount' => 1000.00,
        ]);

        $this->assertDatabaseHas('payroll_deductions', [
            'deduction_name' => 'Income Tax',
            'amount' => 500.00,
        ]);
    }

    public function test_can_list_payroll_entries(): void
    {
        PayrollEntry::factory()->count(3)->create([
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/payroll-entries');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'employee_id', 'basic_salary'],
                ],
            ]);
    }

    public function test_can_show_payroll_entry(): void
    {
        $entry = PayrollEntry::factory()->create([
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/payroll-entries/{$entry->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $entry->id,
                'employee_id' => $this->employee->id,
            ]);
    }

    public function test_can_update_payroll_entry(): void
    {
        $entry = PayrollEntry::factory()->create([
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'status' => 'draft',
            'basic_salary' => 5000.00,
        ]);

        $response = $this->putJson("/api/payroll-entries/{$entry->id}", [
            'basic_salary' => 5500.00,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payroll_entries', [
            'id' => $entry->id,
            'basic_salary' => 5500.00,
        ]);
    }

    public function test_cannot_update_approved_payroll_entry(): void
    {
        $entry = PayrollEntry::factory()->create([
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'status' => 'approved',
        ]);

        $response = $this->putJson("/api/payroll-entries/{$entry->id}", [
            'basic_salary' => 5500.00,
        ]);

        $response->assertStatus(400);
    }

    public function test_can_filter_entries_by_period(): void
    {
        $period2 = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
        ]);

        PayrollEntry::factory()->create([
            'payroll_period_id' => $this->period->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        PayrollEntry::factory()->create([
            'payroll_period_id' => $period2->id,
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/payroll-entries?payroll_period_id={$this->period->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_cannot_access_other_company_payroll_entry(): void
    {
        $otherCompany = Company::factory()->create();
        $otherPeriod = PayrollPeriod::factory()->create([
            'company_id' => $otherCompany->id,
        ]);
        $otherEmployee = User::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $entry = PayrollEntry::factory()->create([
            'payroll_period_id' => $otherPeriod->id,
            'employee_id' => $otherEmployee->id,
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->getJson("/api/payroll-entries/{$entry->id}");

        $response->assertStatus(403);
    }
}
