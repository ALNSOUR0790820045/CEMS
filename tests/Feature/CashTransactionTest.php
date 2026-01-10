<?php

namespace Tests\Feature;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CashTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;
    protected CashAccount $cashAccount;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->base()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->cashAccount = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'current_balance' => 100000
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_receipt_transaction(): void
    {
        $transactionData = [
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'receipt',
            'amount' => 5000,
            'currency_id' => $this->currency->id,
            'description' => 'Test receipt',
            'status' => 'draft',
        ];

        $response = $this->postJson('/api/cash-transactions', $transactionData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'transaction_type' => 'receipt',
                         'amount' => '5000.00',
                     ]
                 ]);

        $this->assertDatabaseHas('cash_transactions', [
            'transaction_type' => 'receipt',
            'amount' => 5000
        ]);
    }

    public function test_can_post_transaction(): void
    {
        $transaction = CashTransaction::create([
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'receipt',
            'amount' => 5000,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $initialBalance = $this->cashAccount->current_balance;

        $response = $this->postJson('/api/cash-transactions/' . $transaction->id . '/post');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Transaction posted successfully',
                 ]);

        $this->cashAccount->refresh();
        $this->assertEquals($initialBalance + 5000, $this->cashAccount->current_balance);
    }

    public function test_cannot_post_payment_with_insufficient_balance(): void
    {
        $transaction = CashTransaction::create([
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'payment',
            'amount' => 200000, // More than available balance
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $response = $this->postJson('/api/cash-transactions/' . $transaction->id . '/post');

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Insufficient balance in cash account',
                 ]);
    }

    public function test_can_cancel_posted_transaction(): void
    {
        $transaction = CashTransaction::create([
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'receipt',
            'amount' => 5000,
            'currency_id' => $this->currency->id,
            'status' => 'posted',
            'posted_by_id' => $this->user->id,
            'posted_at' => now(),
            'company_id' => $this->company->id,
        ]);

        // Update account balance to reflect posted transaction
        $this->cashAccount->current_balance += 5000;
        $this->cashAccount->save();

        $balanceBeforeCancel = $this->cashAccount->current_balance;

        $response = $this->postJson('/api/cash-transactions/' . $transaction->id . '/cancel');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Transaction cancelled successfully',
                 ]);

        $transaction->refresh();
        $this->assertEquals('cancelled', $transaction->status);

        $this->cashAccount->refresh();
        $this->assertEquals($balanceBeforeCancel - 5000, $this->cashAccount->current_balance);
    }

    public function test_generates_unique_transaction_number(): void
    {
        $transaction1 = CashTransaction::create([
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'receipt',
            'amount' => 5000,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $transaction2 = CashTransaction::create([
            'transaction_date' => now()->toDateString(),
            'cash_account_id' => $this->cashAccount->id,
            'transaction_type' => 'payment',
            'amount' => 3000,
            'currency_id' => $this->currency->id,
            'status' => 'draft',
            'company_id' => $this->company->id,
        ]);

        $this->assertNotEquals($transaction1->transaction_number, $transaction2->transaction_number);
        $this->assertMatchesRegularExpression('/^CT-\d{4}-\d{4}$/', $transaction1->transaction_number);
    }
}
