<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\GlAccount;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\CostCenter;
use App\Models\BankAccount;
use App\Models\ApInvoice;
use App\Models\ApInvoiceItem;

class ApSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a company if not exists
        $company = Company::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name' => 'Demo Company',
                'name_en' => 'Demo Company',
                'email' => 'demo@company.com',
                'phone' => '+1234567890',
                'country' => 'US',
                'is_active' => true,
            ]
        );

        // Create a user if not exists
        $user = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );

        // Create currencies
        $usd = Currency::firstOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000,
                'is_active' => true,
            ]
        );

        $eur = Currency::firstOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.9200,
                'is_active' => true,
            ]
        );

        // Create GL Accounts
        $glAccounts = [
            ['account_code' => '2100', 'name' => 'Accounts Payable', 'account_type' => 'liability'],
            ['account_code' => '5100', 'name' => 'Office Expenses', 'account_type' => 'expense'],
            ['account_code' => '5200', 'name' => 'Travel Expenses', 'account_type' => 'expense'],
        ];

        $glAccountModels = [];
        foreach ($glAccounts as $account) {
            $glAccountModels[] = GlAccount::firstOrCreate(
                ['account_code' => $account['account_code'], 'company_id' => $company->id],
                array_merge($account, [
                    'company_id' => $company->id,
                    'is_active' => true,
                ])
            );
        }

        // Create Vendors
        $vendors = [
            ['vendor_code' => 'VEN001', 'name' => 'ABC Supplies Inc', 'email' => 'contact@abcsupplies.com'],
            ['vendor_code' => 'VEN002', 'name' => 'Tech Solutions Ltd', 'email' => 'info@techsolutions.com'],
            ['vendor_code' => 'VEN003', 'name' => 'Office Depot', 'email' => 'sales@officedepot.com'],
        ];

        $vendorModels = [];
        foreach ($vendors as $vendor) {
            $vendorModels[] = Vendor::firstOrCreate(
                ['vendor_code' => $vendor['vendor_code'], 'company_id' => $company->id],
                array_merge($vendor, [
                    'company_id' => $company->id,
                    'payment_terms' => 'net_30',
                    'is_active' => true,
                ])
            );
        }

        // Create Projects
        $project = Project::firstOrCreate(
            ['project_code' => 'PRJ001', 'company_id' => $company->id],
            [
                'name' => 'Office Renovation',
                'description' => 'Renovation of main office',
                'status' => 'active',
                'company_id' => $company->id,
                'start_date' => now()->subMonths(2),
            ]
        );

        // Create Cost Centers
        $costCenter = CostCenter::firstOrCreate(
            ['code' => 'CC001', 'company_id' => $company->id],
            [
                'name' => 'Administration',
                'description' => 'Administrative department',
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );

        // Create Bank Account
        $bankAccount = BankAccount::firstOrCreate(
            ['account_number' => '1234567890', 'company_id' => $company->id],
            [
                'account_name' => 'Operating Account',
                'bank_name' => 'Demo Bank',
                'currency_id' => $usd->id,
                'balance' => 50000.00,
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );

        // Create sample invoices
        $statuses = ['draft', 'pending', 'approved'];
        for ($i = 1; $i <= 5; $i++) {
            $invoice = ApInvoice::create([
                'invoice_date' => now()->subDays(rand(10, 60)),
                'due_date' => now()->addDays(rand(1, 30)),
                'vendor_id' => $vendorModels[array_rand($vendorModels)]->id,
                'project_id' => rand(0, 1) ? $project->id : null,
                'currency_id' => $usd->id,
                'exchange_rate' => 1.0000,
                'subtotal' => $subtotal = rand(1000, 5000),
                'tax_amount' => $taxAmount = $subtotal * 0.1,
                'discount_amount' => 0,
                'payment_terms' => 'net_30',
                'gl_account_id' => $glAccountModels[0]->id,
                'status' => $statuses[array_rand($statuses)],
                'company_id' => $company->id,
                'created_by_id' => $user->id,
            ]);

            // Create invoice items
            for ($j = 1; $j <= rand(1, 3); $j++) {
                ApInvoiceItem::create([
                    'ap_invoice_id' => $invoice->id,
                    'description' => 'Sample Item ' . $j,
                    'quantity' => $qty = rand(1, 10),
                    'unit_price' => $price = rand(100, 500),
                    'gl_account_id' => $glAccountModels[array_rand($glAccountModels)]->id,
                    'project_id' => rand(0, 1) ? $project->id : null,
                    'cost_center_id' => rand(0, 1) ? $costCenter->id : null,
                ]);
            }
        }

        $this->command->info('AP sample data created successfully.');
    }
}
