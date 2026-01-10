<?php

namespace Tests\Feature;

use App\Models\ARInvoice;
use App\Models\ARReceipt;
use App\Models\Client;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ARReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $client;
    protected $currency;

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

        $this->client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1,
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);
    }

    public function test_can_create_receipt(): void
    {
        $data = [
            'receipt_date' => '2026-01-03',
            'client_id' => $this->client->id,
            'payment_method' => 'bank_transfer',
            'amount' => 1000,
            'currency_id' => $this->currency->id,
            'reference_number' => 'REF-123',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ar-receipts', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('a_r_receipts', [
            'client_id' => $this->client->id,
            'amount' => 1000,
        ]);
    }

    public function test_can_allocate_receipt_to_invoice(): void
    {
        $invoice = ARInvoice::create([
            'invoice_number' => 'ARI-2026-0001',
            'invoice_date' => '2026-01-03',
            'due_date' => '2026-02-03',
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 1000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 1000,
            'received_amount' => 0,
            'balance' => 1000,
            'status' => 'sent',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $receipt = ARReceipt::create([
            'receipt_number' => 'ARR-2026-0001',
            'receipt_date' => '2026-01-03',
            'client_id' => $this->client->id,
            'payment_method' => 'bank_transfer',
            'amount' => 1000,
            'currency_id' => $this->currency->id,
            'status' => 'pending',
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $data = [
            'allocations' => [
                [
                    'a_r_invoice_id' => $invoice->id,
                    'allocated_amount' => 1000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/ar-receipts/{$receipt->id}/allocate", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('a_r_receipt_allocations', [
            'a_r_receipt_id' => $receipt->id,
            'a_r_invoice_id' => $invoice->id,
            'allocated_amount' => 1000,
        ]);

        // Check invoice received amount updated
        $invoice->refresh();
        $this->assertEquals(1000, $invoice->received_amount);
    }

    public function test_can_list_receipts(): void
    {
        ARReceipt::create([
            'receipt_number' => 'ARR-2026-0001',
            'receipt_date' => '2026-01-03',
            'client_id' => $this->client->id,
            'payment_method' => 'bank_transfer',
            'amount' => 1000,
            'currency_id' => $this->currency->id,
            'status' => 'pending',
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/ar-receipts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'receipt_number',
                    'receipt_date',
                    'client',
                    'amount',
                    'status',
                ],
            ],
        ]);
    }
}
