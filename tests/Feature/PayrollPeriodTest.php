<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\PayrollPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company
        $this->company = Company::factory()->create();

        // Create a user
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_create_payroll_period(): void
    {
        $response = $this->postJson('/api/payroll-periods', [
            'period_name' => 'January 2026',
            'period_type' => 'monthly',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'payment_date' => '2026-02-01',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'period_name',
                    'period_type',
                    'status',
                    'company_id',
                ],
            ]);

        $this->assertDatabaseHas('payroll_periods', [
            'period_name' => 'January 2026',
            'status' => 'open',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_list_payroll_periods(): void
    {
        PayrollPeriod::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/payroll-periods');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'period_name', 'status'],
                ],
            ]);
    }

    public function test_can_show_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/payroll-periods/{$period->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $period->id,
                'period_name' => $period->period_name,
            ]);
    }

    public function test_can_update_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'open',
        ]);

        $response = $this->putJson("/api/payroll-periods/{$period->id}", [
            'period_name' => 'Updated Period Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payroll_periods', [
            'id' => $period->id,
            'period_name' => 'Updated Period Name',
        ]);
    }

    public function test_cannot_update_non_open_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'calculated',
        ]);

        $response = $this->putJson("/api/payroll-periods/{$period->id}", [
            'period_name' => 'Updated Period Name',
        ]);

        $response->assertStatus(400);
    }

    public function test_can_delete_open_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'open',
        ]);

        $response = $this->deleteJson("/api/payroll-periods/{$period->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('payroll_periods', [
            'id' => $period->id,
        ]);
    }

    public function test_cannot_delete_non_open_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'calculated',
        ]);

        $response = $this->deleteJson("/api/payroll-periods/{$period->id}");

        $response->assertStatus(400);
    }

    public function test_can_calculate_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'open',
        ]);

        $response = $this->postJson("/api/payroll-periods/{$period->id}/calculate");

        $response->assertStatus(200);

        $period->refresh();
        $this->assertEquals('calculated', $period->status);
        $this->assertEquals($this->user->id, $period->calculated_by_id);
    }

    public function test_can_approve_payroll_period(): void
    {
        $period = PayrollPeriod::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'calculated',
        ]);

        $response = $this->postJson("/api/payroll-periods/{$period->id}/approve");

        $response->assertStatus(200);

        $period->refresh();
        $this->assertEquals('approved', $period->status);
        $this->assertEquals($this->user->id, $period->approved_by_id);
    }

    public function test_cannot_access_other_company_payroll_period(): void
    {
        $otherCompany = Company::factory()->create();
        $period = PayrollPeriod::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->getJson("/api/payroll-periods/{$period->id}");

        $response->assertStatus(403);
    }
}
