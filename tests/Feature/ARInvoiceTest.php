<?php

namespace Tests\Feature;

use App\Models\ARInvoice;
use App\Models\Client;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ARInvoiceTest extends TestCase
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

    public function test_can_create_invoice(): void
    {
        $data = [
            'invoice_date' => '2026-01-03',
            'due_date' => '2026-02-03',
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 1000,
            'tax_amount' => 150,
            'discount_amount' => 0,
            'payment_terms' => 'net_30',
            'items' => [
                [
                    'description' => 'Test Item 1',
                    'quantity' => 1,
                    'unit_price' => 500,
                ],
                [
                    'description' => 'Test Item 2',
                    'quantity' => 2,
                    'unit_price' => 250,
                ],
            ],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ar-invoices', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('a_r_invoices', [
            'client_id' => $this->client->id,
            'subtotal' => 1000,
            'total_amount' => 1150,
        ]);
    }

    public function test_can_list_invoices(): void
    {
        ARInvoice::create([
            'invoice_number' => 'ARI-2026-0001',
            'invoice_date' => '2026-01-03',
            'due_date' => '2026-02-03',
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 1000,
            'tax_amount' => 150,
            'discount_amount' => 0,
            'total_amount' => 1150,
            'received_amount' => 0,
            'balance' => 1150,
            'status' => 'draft',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/ar-invoices');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'invoice_number',
                    'invoice_date',
                    'client',
                    'total_amount',
                    'status',
                ],
            ],
        ]);
    }

    public function test_can_send_invoice(): void
    {
        $invoice = ARInvoice::create([
            'invoice_number' => 'ARI-2026-0001',
            'invoice_date' => '2026-01-03',
            'due_date' => '2026-02-03',
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 1000,
            'tax_amount' => 150,
            'discount_amount' => 0,
            'total_amount' => 1150,
            'received_amount' => 0,
            'balance' => 1150,
            'status' => 'draft',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/ar-invoices/{$invoice->id}/send");

        $response->assertStatus(200);
        $this->assertDatabaseHas('a_r_invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
        ]);
    }
}
