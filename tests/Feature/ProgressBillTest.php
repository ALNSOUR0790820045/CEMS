<?php

namespace Tests\Feature;

use App\Models\ProgressBill;
use App\Models\ProgressBillItem;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Models\BoqItem;
use App\Models\BOQHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressBillTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $contract;
    protected $currency;
    protected $boqHeader;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'name' => 'Test Project',
            'code' => 'TP001',
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->contract = Contract::create([
            'contract_number' => 'CT-2026-001',
            'name' => 'Test Contract',
            'client_id' => 1,
            'contract_value' => 1000000,
            'currency_id' => $this->currency->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'duration_days' => 365,
            'company_id' => $this->company->id,
        ]);

        $this->boqHeader = BOQHeader::create([
            'project_id' => $this->project->id,
            'boq_number' => 'BOQ-001',
            'title' => 'Test BOQ',
            'currency_id' => $this->currency->id,
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_progress_bill(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'currency_id' => $this->currency->id,
            'retention_percentage' => 10,
            'vat_percentage' => 15,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/progress-bills', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'bill_number',
                'project_id',
                'contract_id',
                'bill_sequence',
                'status',
            ]);

        $this->assertDatabaseHas('progress_bills', [
            'project_id' => $this->project->id,
            'bill_sequence' => 1,
            'status' => 'draft',
        ]);
    }

    public function test_can_view_progress_bill(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/progress-bills/{$bill->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $bill->id,
                'bill_number' => 'PB-2026-001',
            ]);
    }

    public function test_can_update_progress_bill(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $updateData = [
            'retention_percentage' => 5,
            'vat_percentage' => 15,
            'notes' => 'Updated notes',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/progress-bills/{$bill->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('progress_bills', [
            'id' => $bill->id,
            'retention_percentage' => 5,
            'vat_percentage' => 15,
        ]);
    }

    public function test_cannot_update_submitted_bill(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'submitted',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $updateData = ['notes' => 'Updated notes'];

        $response = $this->actingAs($this->user)
            ->putJson("/api/progress-bills/{$bill->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_can_calculate_bill_amounts(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'retention_percentage' => 10,
            'vat_percentage' => 15,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        ProgressBillItem::create([
            'progress_bill_id' => $bill->id,
            'item_code' => '1.1',
            'description' => 'Test Item',
            'contract_quantity' => 100,
            'contract_rate' => 100,
            'contract_amount' => 10000,
            'current_quantity' => 50,
            'current_amount' => 5000,
        ]);

        $bill->calculateAmounts();

        $this->assertEquals(5000, $bill->current_amount);
        $this->assertEquals(500, $bill->retention_amount); // 10% of 5000
        $this->assertEquals(4500, $bill->net_amount); // 5000 - 500
        $this->assertEquals(675, $bill->vat_amount); // 15% of 4500
        $this->assertEquals(5175, $bill->total_payable); // 4500 + 675
    }

    public function test_can_submit_progress_bill(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        // Add at least one item
        ProgressBillItem::create([
            'progress_bill_id' => $bill->id,
            'item_code' => '1.1',
            'description' => 'Test Item',
            'contract_quantity' => 100,
            'contract_rate' => 100,
            'contract_amount' => 10000,
            'current_quantity' => 50,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/submit");

        $response->assertStatus(200);

        $this->assertDatabaseHas('progress_bills', [
            'id' => $bill->id,
            'status' => 'submitted',
        ]);
    }

    public function test_workflow_approval_stages(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'submitted',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        // Review
        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/review");
        $response->assertStatus(200);
        $this->assertDatabaseHas('progress_bills', ['id' => $bill->id, 'status' => 'reviewed']);

        // Certify
        $bill->refresh();
        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/certify");
        $response->assertStatus(200);
        $this->assertDatabaseHas('progress_bills', ['id' => $bill->id, 'status' => 'certified']);

        // Approve
        $bill->refresh();
        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/approve");
        $response->assertStatus(200);
        $this->assertDatabaseHas('progress_bills', ['id' => $bill->id, 'status' => 'approved']);
    }

    public function test_can_reject_progress_bill(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'submitted',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/reject", [
                'rejection_reason' => 'Incorrect calculations',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('progress_bills', [
            'id' => $bill->id,
            'status' => 'rejected',
            'rejection_reason' => 'Incorrect calculations',
        ]);
    }

    public function test_can_mark_bill_as_paid(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'approved',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/progress-bills/{$bill->id}/mark-paid", [
                'payment_reference' => 'PAY-2026-001',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('progress_bills', [
            'id' => $bill->id,
            'status' => 'paid',
            'payment_reference' => 'PAY-2026-001',
        ]);
    }

    public function test_generate_bill_number(): void
    {
        $bill = new ProgressBill([
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $billNumber = $bill->generateBillNumber();
        
        $this->assertStringStartsWith('PB-2026-', $billNumber);
        $this->assertEquals(11, strlen($billNumber)); // PB-YYYY-XXX
    }

    public function test_can_get_billing_summary_report(): void
    {
        $bill1 = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'paid',
            'current_amount' => 10000,
            'retention_amount' => 1000,
            'total_payable' => 9000,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/reports/billing-summary/{$this->project->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'project',
                'total_bills',
                'total_billed',
                'total_retention',
                'total_paid',
                'bills',
            ]);
    }

    public function test_can_get_cash_flow_report(): void
    {
        $bill = ProgressBill::create([
            'bill_number' => 'PB-2026-001',
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'bill_date' => '2026-02-01',
            'bill_type' => 'interim',
            'bill_sequence' => 1,
            'currency_id' => $this->currency->id,
            'status' => 'paid',
            'current_amount' => 10000,
            'retention_amount' => 1000,
            'net_amount' => 9000,
            'vat_amount' => 1350,
            'total_payable' => 10350,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/reports/cash-flow/{$this->project->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_billed',
                'total_retention',
                'total_deductions',
                'total_payable',
                'total_paid',
                'total_outstanding',
                'cash_flow',
            ]);
    }
}
