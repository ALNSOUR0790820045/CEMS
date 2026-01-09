<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\PettyCashAccount;
use App\Models\PettyCashTransaction;
use App\Models\PettyCashReplenishment;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PettyCashTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        // Authenticate user for API requests
        Sanctum::actingAs($this->user);
    }

    /**
     * Test can create a petty cash account.
     */
    public function test_can_create_petty_cash_account(): void
    {
        $custodian = User::factory()->create(['company_id' => $this->company->id]);
        
        $accountData = [
            'account_code' => 'PC-001',
            'account_name' => 'Office Petty Cash',
            'custodian_id' => $custodian->id,
            'float_amount' => 5000.00,
            'minimum_balance' => 500.00,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/petty-cash-accounts', $accountData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'account_code', 'account_name', 'custodian_id',
                     'float_amount', 'current_balance', 'minimum_balance'
                 ])
                 ->assertJson([
                     'account_code' => 'PC-001',
                     'account_name' => 'Office Petty Cash',
                     'float_amount' => '5000.00',
                     'current_balance' => '5000.00',
                 ]);

        $this->assertDatabaseHas('petty_cash_accounts', [
            'account_code' => 'PC-001',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test can create expense transaction and check balance.
     */
    public function test_can_create_expense_and_check_balance(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
            'float_amount' => 5000.00,
            'current_balance' => 5000.00,
        ]);

        $category = ExpenseCategory::factory()->create([
            'company_id' => $this->company->id,
            'spending_limit' => 1000.00,
        ]);

        // Create expense transaction
        $transactionData = [
            'transaction_date' => now()->toDateString(),
            'petty_cash_account_id' => $account->id,
            'transaction_type' => 'expense',
            'amount' => 200.00,
            'description' => 'Office supplies',
            'expense_category_id' => $category->id,
            'payee_name' => 'Stationery Store',
        ];

        $response = $this->postJson('/api/petty-cash-transactions', $transactionData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'transaction_number', 'transaction_date',
                     'petty_cash_account_id', 'transaction_type', 'amount', 'status'
                 ])
                 ->assertJson([
                     'transaction_type' => 'expense',
                     'amount' => '200.00',
                     'status' => 'pending',
                 ]);

        // Approve transaction
        $transactionId = $response->json('id');
        $approveResponse = $this->postJson("/api/petty-cash-transactions/{$transactionId}/approve");

        $approveResponse->assertStatus(200)
                       ->assertJson(['status' => 'approved']);

        // Check balance was updated
        $account->refresh();
        $this->assertEquals(4800.00, $account->current_balance);
    }

    /**
     * Test cannot expense more than available balance.
     */
    public function test_cannot_expense_more_than_balance(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
            'float_amount' => 1000.00,
            'current_balance' => 500.00,
        ]);

        $transactionData = [
            'transaction_date' => now()->toDateString(),
            'petty_cash_account_id' => $account->id,
            'transaction_type' => 'expense',
            'amount' => 600.00,
            'description' => 'Exceeds balance',
        ];

        $response = $this->postJson('/api/petty-cash-transactions', $transactionData);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Insufficient balance in petty cash account']);
    }

    /**
     * Test can request replenishment.
     */
    public function test_can_request_replenishment(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
            'float_amount' => 5000.00,
            'current_balance' => 500.00,
        ]);

        $replenishmentData = [
            'replenishment_date' => now()->toDateString(),
            'petty_cash_account_id' => $account->id,
            'amount' => 4500.00,
            'payment_method' => 'transfer',
            'reference_number' => 'REF-12345',
            'notes' => 'Monthly replenishment',
        ];

        $response = $this->postJson('/api/petty-cash-replenishments', $replenishmentData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'replenishment_number', 'replenishment_date',
                     'petty_cash_account_id', 'amount', 'status'
                 ])
                 ->assertJson([
                     'amount' => '4500.00',
                     'status' => 'pending',
                 ]);

        $this->assertDatabaseHas('petty_cash_replenishments', [
            'petty_cash_account_id' => $account->id,
            'amount' => 4500.00,
            'status' => 'pending',
        ]);
    }

    /**
     * Test can reject a transaction.
     */
    public function test_can_reject_transaction(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
        ]);

        $transaction = PettyCashTransaction::factory()->create([
            'company_id' => $this->company->id,
            'petty_cash_account_id' => $account->id,
            'requested_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/petty-cash-transactions/{$transaction->id}/reject", [
            'rejection_reason' => 'Invalid receipt',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'rejected']);

        $this->assertDatabaseHas('petty_cash_transactions', [
            'id' => $transaction->id,
            'status' => 'rejected',
        ]);
    }

    /**
     * Test can get account balance.
     */
    public function test_can_get_account_balance(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
            'float_amount' => 5000.00,
            'current_balance' => 3000.00,
            'minimum_balance' => 500.00,
        ]);

        $response = $this->getJson("/api/petty-cash-accounts/{$account->id}/balance");

        $response->assertStatus(200)
                 ->assertJson([
                     'account_code' => $account->account_code,
                     'current_balance' => '3000.00',
                     'float_amount' => '5000.00',
                     'minimum_balance' => '500.00',
                     'is_low_balance' => false,
                 ]);
    }

    /**
     * Test low balance warning.
     */
    public function test_detects_low_balance(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
            'float_amount' => 5000.00,
            'current_balance' => 400.00,
            'minimum_balance' => 500.00,
        ]);

        $response = $this->getJson("/api/petty-cash-accounts/{$account->id}/balance");

        $response->assertStatus(200)
                 ->assertJson([
                     'is_low_balance' => true,
                 ]);
    }

    /**
     * Test expense category requires receipt validation.
     */
    public function test_category_requires_receipt_validation(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
        ]);

        $category = ExpenseCategory::factory()->create([
            'company_id' => $this->company->id,
            'requires_receipt' => true,
        ]);

        $transactionData = [
            'transaction_date' => now()->toDateString(),
            'petty_cash_account_id' => $account->id,
            'transaction_type' => 'expense',
            'amount' => 100.00,
            'description' => 'Test expense',
            'expense_category_id' => $category->id,
            // No receipt_number provided
        ];

        $response = $this->postJson('/api/petty-cash-transactions', $transactionData);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Receipt is required for this expense category']);
    }

    /**
     * Test spending limit validation.
     */
    public function test_spending_limit_validation(): void
    {
        $account = PettyCashAccount::factory()->create([
            'company_id' => $this->company->id,
            'custodian_id' => $this->user->id,
        ]);

        $category = ExpenseCategory::factory()->create([
            'company_id' => $this->company->id,
            'spending_limit' => 500.00,
        ]);

        $transactionData = [
            'transaction_date' => now()->toDateString(),
            'petty_cash_account_id' => $account->id,
            'transaction_type' => 'expense',
            'amount' => 600.00,
            'description' => 'Exceeds limit',
            'expense_category_id' => $category->id,
        ];

        $response = $this->postJson('/api/petty-cash-transactions', $transactionData);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Amount exceeds spending limit for this category']);
    }
}
