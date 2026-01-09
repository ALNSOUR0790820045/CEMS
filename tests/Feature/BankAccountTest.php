<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_bank_accounts(): void
    {
        BankAccount::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/bank-accounts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'account_number', 'account_name', 'bank_name']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_bank_account(): void
    {
        $bankAccountData = [
            'account_number' => 'ACC-001',
            'account_name' => 'Main Account',
            'bank_name' => 'Test Bank',
            'branch' => 'Main Branch',
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/bank-accounts', $bankAccountData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'account_number' => 'ACC-001',
                         'account_name' => 'Main Account',
                     ]
                 ]);

        $this->assertDatabaseHas('bank_accounts', ['account_number' => 'ACC-001']);
    }

    public function test_can_show_bank_account(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/bank-accounts/' . $bankAccount->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $bankAccount->id,
                         'account_number' => $bankAccount->account_number,
                     ]
                 ]);
    }

    public function test_can_update_bank_account(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $updateData = [
            'account_name' => 'Updated Account Name',
        ];

        $response = $this->putJson('/api/bank-accounts/' . $bankAccount->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'account_name' => 'Updated Account Name',
                     ]
                 ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bankAccount->id,
            'account_name' => 'Updated Account Name',
        ]);
    }

    public function test_can_delete_bank_account(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->deleteJson('/api/bank-accounts/' . $bankAccount->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertSoftDeleted('bank_accounts', ['id' => $bankAccount->id]);
    }

    public function test_can_get_bank_account_balance(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'current_balance' => 10000,
            'bank_balance' => 9500,
        ]);

        $response = $this->getJson('/api/bank-accounts/' . $bankAccount->id . '/balance');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'current_balance' => '10000.00',
                         'bank_balance' => '9500.00',
                         'difference' => 500,
                     ]
                 ]);
    }
}
