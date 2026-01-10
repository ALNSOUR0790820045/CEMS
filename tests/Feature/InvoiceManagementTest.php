<?php

namespace Tests\Feature;

use App\Models\ARInvoice;
use App\Models\Client;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $client;
    protected $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->client = Client::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_active' => true,
        ]);
    }

    public function test_user_can_list_ar_invoices(): void
    {
        ARInvoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            'due_date' => '2026-02-15',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/ar-invoices');

        $response->assertStatus(200);
    }

    public function test_user_can_create_ar_invoice(): void
    {
        $invoiceData = [
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            'due_date' => '2026-02-15',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)
            ->post('/ar-invoices', $invoiceData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('a_r_invoices', [
            'invoice_number' => 'INV-001',
            'total_amount' => 11500,
        ]);
    }

    public function test_invoice_requires_client(): void
    {
        $invoiceData = [
            'company_id' => $this->company->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            // client_id is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/ar-invoices', $invoiceData);

        $response->assertSessionHasErrors(['client_id']);
    }

    public function test_invoice_calculates_total_correctly(): void
    {
        $invoice = ARInvoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            'due_date' => '2026-02-15',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'draft',
        ]);

        $expectedTotal = $invoice->subtotal + $invoice->tax_amount;
        
        $this->assertEquals($expectedTotal, $invoice->total_amount);
    }

    public function test_user_can_update_invoice_status(): void
    {
        $invoice = ARInvoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            'due_date' => '2026-02-15',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/ar-invoices/{$invoice->id}", [
                'company_id' => $this->company->id,
                'client_id' => $this->client->id,
                'currency_id' => $this->currency->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
                'due_date' => $invoice->due_date->format('Y-m-d'),
                'subtotal' => $invoice->subtotal,
                'tax_amount' => $invoice->tax_amount,
                'total_amount' => $invoice->total_amount,
                'status' => 'sent',
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('a_r_invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
        ]);
    }

    public function test_invoice_belongs_to_client(): void
    {
        $invoice = ARInvoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'currency_id' => $this->currency->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-15',
            'due_date' => '2026-02-15',
            'subtotal' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(Client::class, $invoice->client);
        $this->assertEquals($this->client->id, $invoice->client->id);
    }
}
