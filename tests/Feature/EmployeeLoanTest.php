<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\EmployeeLoan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeLoanTest extends TestCase
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

        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_employee_loan(): void
    {
        $response = $this->postJson('/api/employee-loans', [
            'employee_id' => $this->employee->id,
            'loan_date' => '2026-01-01',
            'loan_amount' => 10000.00,
            'installment_amount' => 1000.00,
            'total_installments' => 10,
            'notes' => 'Emergency loan',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'employee_id',
                    'loan_amount',
                    'installment_amount',
                    'total_installments',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('employee_loans', [
            'employee_id' => $this->employee->id,
            'loan_amount' => 10000.00,
            'status' => 'active',
        ]);
    }

    public function test_can_list_employee_loans(): void
    {
        EmployeeLoan::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/employee-loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'employee_id', 'loan_amount', 'status'],
                ],
            ]);
    }

    public function test_can_show_employee_loan(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/employee-loans/{$loan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $loan->id,
                'employee_id' => $this->employee->id,
            ]);
    }

    public function test_can_update_employee_loan(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'status' => 'active',
            'loan_amount' => 10000.00,
        ]);

        $response = $this->putJson("/api/employee-loans/{$loan->id}", [
            'loan_amount' => 12000.00,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('employee_loans', [
            'id' => $loan->id,
            'loan_amount' => 12000.00,
        ]);
    }

    public function test_cannot_update_non_active_loan(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'status' => 'completed',
        ]);

        $response = $this->putJson("/api/employee-loans/{$loan->id}", [
            'loan_amount' => 12000.00,
        ]);

        $response->assertStatus(400);
    }

    public function test_can_cancel_employee_loan(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/employee-loans/{$loan->id}/cancel");

        $response->assertStatus(200);

        $loan->refresh();
        $this->assertEquals('cancelled', $loan->status);
    }

    public function test_cannot_delete_loan_with_paid_installments(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'paid_installments' => 3,
        ]);

        $response = $this->deleteJson("/api/employee-loans/{$loan->id}");

        $response->assertStatus(400);
    }

    public function test_can_delete_loan_without_paid_installments(): void
    {
        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
            'paid_installments' => 0,
        ]);

        $response = $this->deleteJson("/api/employee-loans/{$loan->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('employee_loans', [
            'id' => $loan->id,
        ]);
    }

    public function test_can_filter_loans_by_employee(): void
    {
        $employee2 = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        EmployeeLoan::factory()->create([
            'employee_id' => $this->employee->id,
            'company_id' => $this->company->id,
        ]);

        EmployeeLoan::factory()->create([
            'employee_id' => $employee2->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/employee-loans?employee_id={$this->employee->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_cannot_access_other_company_loan(): void
    {
        $otherCompany = Company::factory()->create();
        $otherEmployee = User::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $loan = EmployeeLoan::factory()->create([
            'employee_id' => $otherEmployee->id,
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->getJson("/api/employee-loans/{$loan->id}");

        $response->assertStatus(403);
    }
}
