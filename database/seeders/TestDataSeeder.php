<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\GLAccount;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::create([
            'name' => 'Test Company',
            'name_en' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'phone' => '+962791234567',
            'address' => '123 Test Street',
            'city' => 'Amman',
            'country' => 'JO',
            'commercial_registration' => '12345',
            'tax_number' => 'TAX-12345',
            'is_active' => true,
        ]);

        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone' => '+962791234567',
            'job_title' => 'Accountant',
            'employee_id' => 'EMP001',
            'is_active' => true,
            'language' => 'en',
            'company_id' => $company->id,
        ]);

        // Get currencies
        $usd = Currency::where('code', 'USD')->first();
        $jod = Currency::where('code', 'JOD')->first();

        // Create GL accounts
        $cashGLAccount = GLAccount::create([
            'account_code' => '1010',
            'account_name' => 'Cash on Hand',
            'account_type' => 'asset',
            'balance' => 0,
            'is_active' => true,
            'company_id' => $company->id,
        ]);

        $bankGLAccount = GLAccount::create([
            'account_code' => '1020',
            'account_name' => 'Bank Account',
            'account_type' => 'asset',
            'balance' => 0,
            'is_active' => true,
            'company_id' => $company->id,
        ]);

        // Create cash accounts
        $cashAccount = CashAccount::create([
            'account_code' => 'CA-001',
            'account_name' => 'Main Cash Account',
            'account_type' => 'cash',
            'currency_id' => $jod->id,
            'current_balance' => 10000.00,
            'gl_account_id' => $cashGLAccount->id,
            'is_active' => true,
            'company_id' => $company->id,
        ]);

        $bankAccount = CashAccount::create([
            'account_code' => 'BA-001',
            'account_name' => 'Main Bank Account',
            'account_type' => 'bank',
            'currency_id' => $usd->id,
            'current_balance' => 50000.00,
            'gl_account_id' => $bankGLAccount->id,
            'is_active' => true,
            'company_id' => $company->id,
        ]);

        $pettyAccount = CashAccount::create([
            'account_code' => 'PC-001',
            'account_name' => 'Petty Cash',
            'account_type' => 'petty_cash',
            'currency_id' => $jod->id,
            'current_balance' => 1000.00,
            'gl_account_id' => $cashGLAccount->id,
            'is_active' => true,
            'company_id' => $company->id,
        ]);

        // Create sample transactions
        CashTransaction::create([
            'transaction_date' => now()->subDays(5),
            'transaction_type' => 'receipt',
            'cash_account_id' => $cashAccount->id,
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'reference_number' => 'REF-001',
            'payee_payer' => 'Customer A',
            'description' => 'Payment received from Customer A',
            'status' => 'posted',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        CashTransaction::create([
            'transaction_date' => now()->subDays(3),
            'transaction_type' => 'payment',
            'cash_account_id' => $cashAccount->id,
            'amount' => 2000.00,
            'payment_method' => 'cash',
            'reference_number' => 'PAY-001',
            'payee_payer' => 'Vendor B',
            'description' => 'Payment to Vendor B for supplies',
            'status' => 'posted',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        CashTransaction::create([
            'transaction_date' => now()->subDays(1),
            'transaction_type' => 'receipt',
            'cash_account_id' => $bankAccount->id,
            'amount' => 15000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'WIRE-001',
            'payee_payer' => 'Client C',
            'description' => 'Wire transfer from Client C',
            'status' => 'posted',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        CashTransaction::create([
            'transaction_date' => now(),
            'transaction_type' => 'payment',
            'cash_account_id' => $pettyAccount->id,
            'amount' => 150.00,
            'payment_method' => 'cash',
            'reference_number' => 'PC-001',
            'payee_payer' => 'Office Supplies Store',
            'description' => 'Office supplies purchase',
            'status' => 'posted',
            'company_id' => $company->id,
            'created_by_id' => $user->id,
        ]);

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Test User Email: test@example.com');
        $this->command->info('Test User Password: password');
    }
}
