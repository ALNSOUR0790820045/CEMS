<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BankReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;
    protected BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->bankAccount = BankAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_bank_reconciliation(): void
    {
        $reconciliationData = [
            'bank_account_id' => $this->bankAccount->id,
            'reconciliation_date' => '2026-01-31',
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'book_balance' => 10000,
            'bank_balance' => 9500,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/bank-reconciliations', $reconciliationData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'bank_account_id' => $this->bankAccount->id,
                         'status' => 'draft',
                     ]
                 ]);

        $this->assertDatabaseHas('bank_reconciliations', [
            'bank_account_id' => $this->bankAccount->id,
            'book_balance' => 10000,
        ]);
    }

    public function test_reconciliation_number_is_auto_generated(): void
    {
        $reconciliationData = [
            'bank_account_id' => $this->bankAccount->id,
            'reconciliation_date' => '2026-01-31',
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'book_balance' => 10000,
            'bank_balance' => 9500,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/bank-reconciliations', $reconciliationData);

        $response->assertStatus(201);
        
        $reconciliation = BankReconciliation::first();
        $this->assertStringStartsWith('BR-2026-', $reconciliation->reconciliation_number);
    }

    public function test_can_add_reconciliation_item(): void
    {
        $reconciliation = BankReconciliation::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
        ]);

        $itemData = [
            'item_type' => 'outstanding_check',
            'description' => 'Check #123',
            'amount' => 500,
            'transaction_date' => '2026-01-15',
            'reference_number' => 'CHK-123',
        ];

        $response = $this->postJson("/api/bank-reconciliations/{$reconciliation->id}/match-item", $itemData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('reconciliation_items', [
            'bank_reconciliation_id' => $reconciliation->id,
            'item_type' => 'outstanding_check',
        ]);
    }

    public function test_can_complete_reconciliation(): void
    {
        $reconciliation = BankReconciliation::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/bank-reconciliations/{$reconciliation->id}/complete");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('bank_reconciliations', [
            'id' => $reconciliation->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_approve_reconciliation(): void
    {
        $reconciliation = BankReconciliation::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
            'status' => 'completed',
        ]);

        $response = $this->postJson("/api/bank-reconciliations/{$reconciliation->id}/approve");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('bank_reconciliations', [
            'id' => $reconciliation->id,
            'status' => 'approved',
        ]);
    }

    public function test_cannot_approve_draft_reconciliation(): void
    {
        $reconciliation = BankReconciliation::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/bank-reconciliations/{$reconciliation->id}/approve");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_cannot_delete_approved_reconciliation(): void
    {
        $reconciliation = BankReconciliation::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
            'status' => 'approved',
        ]);

        $response = $this->deleteJson("/api/bank-reconciliations/{$reconciliation->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }
}
