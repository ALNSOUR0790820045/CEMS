<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. الأصول (Assets)
        $assets = Account::create([
            'code' => '1',
            'name' => 'الأصول',
            'name_en' => 'Assets',
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 1,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 1.1 الأصول المتداولة
        $currentAssets = Account::create([
            'code' => '1-1',
            'name' => 'الأصول المتداولة',
            'name_en' => 'Current Assets',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 2,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 1.1.1 الصندوق
        Account::create([
            'code' => '1-1-001',
            'name' => 'الصندوق',
            'name_en' => 'Cash',
            'parent_id' => $currentAssets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 1.1.2 البنك
        Account::create([
            'code' => '1-1-002',
            'name' => 'البنك',
            'name_en' => 'Bank',
            'parent_id' => $currentAssets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 1.1.3 المدينون
        Account::create([
            'code' => '1-1-003',
            'name' => 'المدينون',
            'name_en' => 'Accounts Receivable',
            'parent_id' => $currentAssets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 1.2 الأصول الثابتة
        $fixedAssets = Account::create([
            'code' => '1-2',
            'name' => 'الأصول الثابتة',
            'name_en' => 'Fixed Assets',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 2,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 1.2.1 الأراضي
        Account::create([
            'code' => '1-2-001',
            'name' => 'الأراضي',
            'name_en' => 'Land',
            'parent_id' => $fixedAssets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 1.2.2 المباني
        Account::create([
            'code' => '1-2-002',
            'name' => 'المباني',
            'name_en' => 'Buildings',
            'parent_id' => $fixedAssets->id,
            'type' => 'asset',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 2. الخصوم (Liabilities)
        $liabilities = Account::create([
            'code' => '2',
            'name' => 'الخصوم',
            'name_en' => 'Liabilities',
            'type' => 'liability',
            'nature' => 'credit',
            'level' => 1,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 2.1 الخصوم المتداولة
        $currentLiabilities = Account::create([
            'code' => '2-1',
            'name' => 'الخصوم المتداولة',
            'name_en' => 'Current Liabilities',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => 'credit',
            'level' => 2,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 2.1.1 الدائنون
        Account::create([
            'code' => '2-1-001',
            'name' => 'الدائنون',
            'name_en' => 'Accounts Payable',
            'parent_id' => $currentLiabilities->id,
            'type' => 'liability',
            'nature' => 'credit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 2.1.2 قروض قصيرة الأجل
        Account::create([
            'code' => '2-1-002',
            'name' => 'قروض قصيرة الأجل',
            'name_en' => 'Short-term Loans',
            'parent_id' => $currentLiabilities->id,
            'type' => 'liability',
            'nature' => 'credit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 3. حقوق الملكية (Equity)
        $equity = Account::create([
            'code' => '3',
            'name' => 'حقوق الملكية',
            'name_en' => 'Equity',
            'type' => 'equity',
            'nature' => 'credit',
            'level' => 1,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 3.1 رأس المال
        Account::create([
            'code' => '3-1',
            'name' => 'رأس المال',
            'name_en' => 'Capital',
            'parent_id' => $equity->id,
            'type' => 'equity',
            'nature' => 'credit',
            'level' => 2,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 3.2 الأرباح المحتجزة
        Account::create([
            'code' => '3-2',
            'name' => 'الأرباح المحتجزة',
            'name_en' => 'Retained Earnings',
            'parent_id' => $equity->id,
            'type' => 'equity',
            'nature' => 'credit',
            'level' => 2,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 4. الإيرادات (Revenue)
        $revenue = Account::create([
            'code' => '4',
            'name' => 'الإيرادات',
            'name_en' => 'Revenue',
            'type' => 'revenue',
            'nature' => 'credit',
            'level' => 1,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 4.1 إيرادات المبيعات
        Account::create([
            'code' => '4-1',
            'name' => 'إيرادات المبيعات',
            'name_en' => 'Sales Revenue',
            'parent_id' => $revenue->id,
            'type' => 'revenue',
            'nature' => 'credit',
            'level' => 2,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 4.2 إيرادات الخدمات
        Account::create([
            'code' => '4-2',
            'name' => 'إيرادات الخدمات',
            'name_en' => 'Service Revenue',
            'parent_id' => $revenue->id,
            'type' => 'revenue',
            'nature' => 'credit',
            'level' => 2,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 5. المصروفات (Expenses)
        $expenses = Account::create([
            'code' => '5',
            'name' => 'المصروفات',
            'name_en' => 'Expenses',
            'type' => 'expense',
            'nature' => 'debit',
            'level' => 1,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 5.1 مصروفات التشغيل
        $operatingExpenses = Account::create([
            'code' => '5-1',
            'name' => 'مصروفات التشغيل',
            'name_en' => 'Operating Expenses',
            'parent_id' => $expenses->id,
            'type' => 'expense',
            'nature' => 'debit',
            'level' => 2,
            'is_parent' => true,
            'is_active' => true,
        ]);

        // 5.1.1 الرواتب والأجور
        Account::create([
            'code' => '5-1-001',
            'name' => 'الرواتب والأجور',
            'name_en' => 'Salaries and Wages',
            'parent_id' => $operatingExpenses->id,
            'type' => 'expense',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 5.1.2 الإيجارات
        Account::create([
            'code' => '5-1-002',
            'name' => 'الإيجارات',
            'name_en' => 'Rent',
            'parent_id' => $operatingExpenses->id,
            'type' => 'expense',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);

        // 5.1.3 المرافق (كهرباء، ماء، هاتف)
        Account::create([
            'code' => '5-1-003',
            'name' => 'المرافق (كهرباء، ماء، هاتف)',
            'name_en' => 'Utilities',
            'parent_id' => $operatingExpenses->id,
            'type' => 'expense',
            'nature' => 'debit',
            'level' => 3,
            'is_parent' => false,
            'is_active' => true,
        ]);
    }
}

