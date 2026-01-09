<?php

namespace Tests\Feature;

use App\Models\CashAccount;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CashAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->base()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_cash_accounts(): void
    {
        CashAccount::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $response = $this->getJson('/api/cash-accounts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'account_code', 'account_name', 'account_type']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_cash_account(): void
    {
        $accountData = [
            'account_name' => 'Main Cash Account',
            'account_name_en' => 'Main Cash Account EN',
            'account_type' => 'cash',
            'currency_id' => $this->currency->id,
            'opening_balance' => 10000,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/cash-accounts', $accountData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'account_name' => 'Main Cash Account',
                         'account_type' => 'cash',
                     ]
                 ]);

        $this->assertDatabaseHas('cash_accounts', ['account_name' => 'Main Cash Account']);
    }

    public function test_account_creation_requires_validation(): void
    {
        $response = $this->postJson('/api/cash-accounts', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['account_name', 'account_type', 'currency_id']);
    }

    public function test_can_show_cash_account(): void
    {
        $account = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $response = $this->getJson('/api/cash-accounts/' . $account->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $account->id,
                         'account_code' => $account->account_code,
                     ]
                 ]);
    }

    public function test_can_update_cash_account(): void
    {
        $account = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $updateData = [
            'account_name' => 'Updated Cash Account',
        ];

        $response = $this->putJson('/api/cash-accounts/' . $account->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'account_name' => 'Updated Cash Account',
                     ]
                 ]);

        $this->assertDatabaseHas('cash_accounts', [
            'id' => $account->id,
            'account_name' => 'Updated Cash Account',
        ]);
    }

    public function test_can_delete_cash_account(): void
    {
        $account = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $response = $this->deleteJson('/api/cash-accounts/' . $account->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Cash account deleted successfully',
                 ]);

        $this->assertSoftDeleted('cash_accounts', ['id' => $account->id]);
    }

    public function test_can_get_account_balance(): void
    {
        $account = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'current_balance' => 50000,
        ]);

        $response = $this->getJson('/api/cash-accounts/' . $account->id . '/balance');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'account_id' => $account->id,
                         'current_balance' => '50000.00',
                     ]
                 ]);
    }

    public function test_generates_unique_account_code(): void
    {
        $account1 = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $account2 = CashAccount::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id
        ]);

        $this->assertNotEquals($account1->account_code, $account2->account_code);
        $this->assertMatchesRegularExpression('/^CA-\d{4}-\d{4}$/', $account1->account_code);
    }
}
