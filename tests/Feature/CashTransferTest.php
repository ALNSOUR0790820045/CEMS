<?php

namespace Tests\Feature;

use App\Models\CashAccount;
use App\Models\CashTransfer;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CashTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;
    protected CashAccount $fromAccount;
    protected CashAccount $toAccount;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->base()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->fromAccount = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'current_balance' => 100000
        ]);
        $this->toAccount = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'current_balance' => 50000
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_cash_transfer(): void
    {
        $transferData = [
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 10000,
            'notes' => 'Test transfer',
        ];

        $response = $this->postJson('/api/cash-transfers', $transferData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'amount' => '10000.00',
                         'status' => 'pending',
                     ]
                 ]);

        $this->assertDatabaseHas('cash_transfers', [
            'amount' => 10000,
            'status' => 'pending'
        ]);
    }

    public function test_can_approve_transfer(): void
    {
        $transfer = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 10000,
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'status' => 'pending',
            'requested_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson('/api/cash-transfers/' . $transfer->id . '/approve');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Transfer approved successfully',
                 ]);

        $transfer->refresh();
        $this->assertEquals('approved', $transfer->status);
        $this->assertNotNull($transfer->approved_by_id);
        $this->assertNotNull($transfer->approved_at);
    }

    public function test_can_complete_transfer(): void
    {
        $transfer = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 10000,
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'fees' => 100,
            'status' => 'approved',
            'requested_by_id' => $this->user->id,
            'approved_by_id' => $this->user->id,
            'approved_at' => now(),
            'company_id' => $this->company->id,
        ]);

        $fromBalanceBefore = $this->fromAccount->current_balance;
        $toBalanceBefore = $this->toAccount->current_balance;

        $response = $this->postJson('/api/cash-transfers/' . $transfer->id . '/complete');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Transfer completed successfully',
                 ]);

        $transfer->refresh();
        $this->assertEquals('completed', $transfer->status);
        $this->assertNotNull($transfer->completed_at);

        $this->fromAccount->refresh();
        $this->toAccount->refresh();

        $this->assertEquals($fromBalanceBefore - 10100, $this->fromAccount->current_balance); // Amount + fees
        $this->assertEquals($toBalanceBefore + 10000, $this->toAccount->current_balance);
    }

    public function test_cannot_complete_transfer_with_insufficient_balance(): void
    {
        $transfer = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 200000, // More than available
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'status' => 'approved',
            'requested_by_id' => $this->user->id,
            'approved_by_id' => $this->user->id,
            'approved_at' => now(),
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson('/api/cash-transfers/' . $transfer->id . '/complete');

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Insufficient balance in source account',
                 ]);
    }

    public function test_can_cancel_transfer(): void
    {
        $transfer = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 10000,
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'status' => 'pending',
            'requested_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson('/api/cash-transfers/' . $transfer->id . '/cancel');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Transfer cancelled successfully',
                 ]);

        $transfer->refresh();
        $this->assertEquals('cancelled', $transfer->status);
    }

    public function test_generates_unique_transfer_number(): void
    {
        $transfer1 = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 10000,
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'status' => 'pending',
            'requested_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $transfer2 = CashTransfer::create([
            'transfer_date' => now()->toDateString(),
            'from_account_id' => $this->fromAccount->id,
            'to_account_id' => $this->toAccount->id,
            'amount' => 5000,
            'from_currency_id' => $this->currency->id,
            'to_currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'status' => 'pending',
            'requested_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $this->assertNotEquals($transfer1->transfer_number, $transfer2->transfer_number);
        $this->assertMatchesRegularExpression('/^TRF-\d{4}-\d{4}$/', $transfer1->transfer_number);
    }
}
