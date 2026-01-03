<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use App\Models\GlAccount;
use App\Models\Vendor;
use App\Models\ApInvoice;
use Spatie\Permission\Models\Permission;

class ApInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create company
        $this->company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'country' => 'US',
            'is_active' => true,
        ]);

        // Create user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        // Create permissions
        Permission::create(['name' => 'ap_invoices.view']);
        Permission::create(['name' => 'ap_invoices.create']);
        Permission::create(['name' => 'ap_invoices.approve']);
        
        $this->user->givePermissionTo(['ap_invoices.view', 'ap_invoices.create', 'ap_invoices.approve']);
    }

    public function test_can_create_invoice_with_items()
    {
        // Create dependencies
        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $glAccount = GlAccount::create([
            'account_code' => '2100',
            'name' => 'Accounts Payable',
            'account_type' => 'liability',
            'company_id' => $this->company->id,
        ]);

        $vendor = Vendor::create([
            'vendor_code' => 'VEN001',
            'name' => 'Test Vendor',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
        ]);

        $invoiceData = [
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'vendor_id' => $vendor->id,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0000,
            'subtotal' => 1000.00,
            'tax_amount' => 100.00,
            'discount_amount' => 0,
            'payment_terms' => 'net_30',
            'gl_account_id' => $glAccount->id,
            'items' => [
                [
                    'description' => 'Test Item 1',
                    'quantity' => 2,
                    'unit_price' => 500.00,
                ]
            ],
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ap-invoices', $invoiceData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'invoice' => [
                    'id',
                    'invoice_number',
                    'status',
                ]
            ]);

        $this->assertDatabaseHas('ap_invoices', [
            'vendor_id' => $vendor->id,
            'subtotal' => 1000.00,
            'status' => 'draft',
        ]);
    }

    public function test_invoice_has_auto_generated_number()
    {
        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $glAccount = GlAccount::create([
            'account_code' => '2100',
            'name' => 'Accounts Payable',
            'account_type' => 'liability',
            'company_id' => $this->company->id,
        ]);

        $vendor = Vendor::create([
            'vendor_code' => 'VEN001',
            'name' => 'Test Vendor',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
        ]);

        $invoice = ApInvoice::create([
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'vendor_id' => $vendor->id,
            'currency_id' => $currency->id,
            'subtotal' => 1000,
            'payment_terms' => 'net_30',
            'gl_account_id' => $glAccount->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $this->assertNotEmpty($invoice->invoice_number);
        $this->assertStringStartsWith('API-' . date('Y'), $invoice->invoice_number);
    }

    public function test_can_approve_invoice()
    {
        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
        ]);

        $glAccount = GlAccount::create([
            'account_code' => '2100',
            'name' => 'Accounts Payable',
            'account_type' => 'liability',
            'company_id' => $this->company->id,
        ]);

        $vendor = Vendor::create([
            'vendor_code' => 'VEN001',
            'name' => 'Test Vendor',
            'payment_terms' => 'net_30',
            'company_id' => $this->company->id,
        ]);

        $invoice = ApInvoice::create([
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'vendor_id' => $vendor->id,
            'currency_id' => $currency->id,
            'subtotal' => 1000,
            'payment_terms' => 'net_30',
            'gl_account_id' => $glAccount->id,
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/ap-invoices/{$invoice->id}/approve");

        $response->assertStatus(200);
        
        $invoice->refresh();
        $this->assertEquals('approved', $invoice->status);
        $this->assertNotNull($invoice->approved_by_id);
        $this->assertNotNull($invoice->approved_at);
    }
}
