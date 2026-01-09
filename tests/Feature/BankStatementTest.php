<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankStatement;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BankStatementTest extends TestCase
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

    public function test_can_create_bank_statement(): void
    {
        $statementData = [
            'bank_account_id' => $this->bankAccount->id,
            'statement_date' => '2026-01-01',
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'opening_balance' => 10000,
            'closing_balance' => 12000,
            'total_deposits' => 5000,
            'total_withdrawals' => 3000,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/bank-statements', $statementData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'bank_account_id' => $this->bankAccount->id,
                     ]
                 ]);

        $this->assertDatabaseHas('bank_statements', [
            'bank_account_id' => $this->bankAccount->id,
            'opening_balance' => 10000,
        ]);
    }

    public function test_bank_statement_number_is_auto_generated(): void
    {
        $statementData = [
            'bank_account_id' => $this->bankAccount->id,
            'statement_date' => '2026-01-01',
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'opening_balance' => 10000,
            'closing_balance' => 12000,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/bank-statements', $statementData);

        $response->assertStatus(201);
        
        $statement = BankStatement::first();
        $this->assertStringStartsWith('BS-2026-', $statement->statement_number);
    }

    public function test_can_list_bank_statements(): void
    {
        BankStatement::factory()->count(3)->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/bank-statements');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'statement_number', 'bank_account_id']
                         ]
                     ]
                 ]);
    }

    public function test_can_show_bank_statement(): void
    {
        $statement = BankStatement::factory()->create([
            'bank_account_id' => $this->bankAccount->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/bank-statements/' . $statement->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $statement->id,
                         'statement_number' => $statement->statement_number,
                     ]
                 ]);
    }

    public function test_can_create_bank_statement_with_lines(): void
    {
        $statementData = [
            'bank_account_id' => $this->bankAccount->id,
            'statement_date' => '2026-01-01',
            'period_from' => '2026-01-01',
            'period_to' => '2026-01-31',
            'opening_balance' => 10000,
            'closing_balance' => 12000,
            'company_id' => $this->company->id,
            'lines' => [
                [
                    'transaction_date' => '2026-01-05',
                    'description' => 'Deposit',
                    'credit_amount' => 2000,
                    'debit_amount' => 0,
                    'balance' => 12000,
                ],
            ],
        ];

        $response = $this->postJson('/api/bank-statements', $statementData);

        $response->assertStatus(201);
        
        $statement = BankStatement::first();
        $this->assertCount(1, $statement->lines);
    }
}
