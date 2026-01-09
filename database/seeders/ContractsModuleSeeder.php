<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Currency;
use App\Models\GLAccount;
use App\Models\Contract;
use App\Models\ContractChangeOrder;
use App\Models\ContractAmendment;
use App\Models\ContractClause;
use App\Models\ContractMilestone;
use App\Models\Company;
use App\Models\User;

class ContractsModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first company and user
        $company = Company::first();
        $user = User::first();

        if (!$company || !$user) {
            $this->command->error('Please ensure at least one company and user exist before running this seeder.');
            return;
        }

        // Create Currencies
        $this->command->info('Creating currencies...');
        $currencies = [
            ['code' => 'SAR', 'name' => 'ريال سعودي', 'name_en' => 'Saudi Riyal', 'symbol' => 'ر.س', 'exchange_rate' => 1.0000, 'is_base' => true, 'company_id' => $company->id],
            ['code' => 'USD', 'name' => 'دولار أمريكي', 'name_en' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 3.7500, 'is_base' => false, 'company_id' => $company->id],
            ['code' => 'EUR', 'name' => 'يورو', 'name_en' => 'Euro', 'symbol' => '€', 'exchange_rate' => 4.0800, 'is_base' => false, 'company_id' => $company->id],
        ];

        foreach ($currencies as $currencyData) {
            Currency::firstOrCreate(
                ['code' => $currencyData['code'], 'company_id' => $company->id],
                $currencyData
            );
        }

        $sar = Currency::where('code', 'SAR')->where('company_id', $company->id)->first();

        // Create GL Accounts
        $this->command->info('Creating GL accounts...');
        $glAccounts = [
            ['account_code' => '1100', 'account_name' => 'الذمم المدينة', 'account_name_en' => 'Accounts Receivable', 'account_type' => 'asset', 'company_id' => $company->id],
            ['account_code' => '4100', 'account_name' => 'إيرادات العقود', 'account_name_en' => 'Contract Revenue', 'account_type' => 'revenue', 'company_id' => $company->id],
        ];

        $glAccountsCreated = [];
        foreach ($glAccounts as $accountData) {
            $glAccountsCreated[] = GLAccount::firstOrCreate(
                ['account_code' => $accountData['account_code'], 'company_id' => $company->id],
                $accountData
            );
        }

        // Create Clients
        $this->command->info('Creating clients...');
        $clients = [
            [
                'client_code' => 'CLI-2026-0001',
                'name' => 'وزارة الإسكان',
                'name_en' => 'Ministry of Housing',
                'client_type' => 'government',
                'email' => 'info@housing.gov.sa',
                'phone' => '+966112345678',
                'city' => 'الرياض',
                'country' => 'SA',
                'commercial_registration' => '1234567890',
                'company_id' => $company->id,
            ],
            [
                'client_code' => 'CLI-2026-0002',
                'name' => 'شركة التطوير العقاري',
                'name_en' => 'Real Estate Development Company',
                'client_type' => 'private',
                'email' => 'info@realestate.com',
                'phone' => '+966112345679',
                'city' => 'جدة',
                'country' => 'SA',
                'commercial_registration' => '0987654321',
                'company_id' => $company->id,
            ],
            [
                'client_code' => 'CLI-2026-0003',
                'name' => 'شركة البناء المتقدم',
                'name_en' => 'Advanced Construction Company',
                'client_type' => 'private',
                'email' => 'info@advancedcons.com',
                'phone' => '+966112345680',
                'city' => 'الدمام',
                'country' => 'SA',
                'commercial_registration' => '1122334455',
                'company_id' => $company->id,
            ],
        ];

        $clientsCreated = [];
        foreach ($clients as $clientData) {
            $clientsCreated[] = Client::firstOrCreate(
                ['client_code' => $clientData['client_code'], 'company_id' => $company->id],
                $clientData
            );
        }

        // Create Contracts
        $this->command->info('Creating contracts...');
        $contracts = [
            [
                'contract_code' => 'CNT-2026-0001',
                'contract_number' => 'MOH-2026-001',
                'contract_title' => 'إنشاء مجمع سكني - حي النرجس',
                'contract_title_en' => 'Construction of Residential Complex - Al Narjis District',
                'client_id' => $clientsCreated[0]->id,
                'contract_type' => 'lump_sum',
                'contract_category' => 'main_contract',
                'contract_value' => 50000000.00,
                'currency_id' => $sar->id,
                'signing_date' => '2026-01-01',
                'commencement_date' => '2026-01-15',
                'completion_date' => '2027-12-31',
                'defects_liability_period' => 365,
                'retention_percentage' => 5.00,
                'advance_payment_percentage' => 10.00,
                'payment_terms' => 'الدفع بموجب المستخلصات الشهرية، نسبة استبقاء 5%، دفعة مقدمة 10%',
                'penalty_clause' => 'غرامة تأخير بنسبة 0.1% من قيمة العقد عن كل يوم تأخير',
                'scope_of_work' => 'إنشاء مجمع سكني متكامل يتضمن 200 وحدة سكنية مع المرافق والخدمات',
                'contract_status' => 'active',
                'original_contract_value' => 50000000.00,
                'current_contract_value' => 50000000.00,
                'total_change_orders_value' => 0.00,
                'contract_manager_id' => $user->id,
                'project_manager_id' => $user->id,
                'gl_revenue_account_id' => $glAccountsCreated[1]->id,
                'gl_receivable_account_id' => $glAccountsCreated[0]->id,
                'company_id' => $company->id,
            ],
            [
                'contract_code' => 'CNT-2026-0002',
                'contract_number' => 'REC-2026-002',
                'contract_title' => 'بناء برج تجاري - الكورنيش',
                'contract_title_en' => 'Commercial Tower Construction - Corniche',
                'client_id' => $clientsCreated[1]->id,
                'contract_type' => 'unit_price',
                'contract_category' => 'main_contract',
                'contract_value' => 75000000.00,
                'currency_id' => $sar->id,
                'signing_date' => '2026-02-01',
                'commencement_date' => '2026-02-15',
                'completion_date' => '2028-02-15',
                'defects_liability_period' => 365,
                'retention_percentage' => 10.00,
                'advance_payment_percentage' => 15.00,
                'payment_terms' => 'الدفع حسب الكميات المنفذة، نسبة استبقاء 10%',
                'penalty_clause' => 'غرامة تأخير بنسبة 0.15% من قيمة العقد عن كل يوم تأخير',
                'scope_of_work' => 'بناء برج تجاري مكون من 30 طابق مع مواقف سيارات تحت الأرض',
                'contract_status' => 'signed',
                'original_contract_value' => 75000000.00,
                'current_contract_value' => 75000000.00,
                'total_change_orders_value' => 0.00,
                'contract_manager_id' => $user->id,
                'gl_revenue_account_id' => $glAccountsCreated[1]->id,
                'gl_receivable_account_id' => $glAccountsCreated[0]->id,
                'company_id' => $company->id,
            ],
            [
                'contract_code' => 'CNT-2026-0003',
                'contract_number' => 'ABC-2026-003',
                'contract_title' => 'توسعة مصنع - المنطقة الصناعية',
                'contract_title_en' => 'Factory Expansion - Industrial Area',
                'client_id' => $clientsCreated[2]->id,
                'contract_type' => 'cost_plus',
                'contract_category' => 'main_contract',
                'contract_value' => 30000000.00,
                'currency_id' => $sar->id,
                'signing_date' => '2026-01-20',
                'commencement_date' => '2026-02-01',
                'completion_date' => '2026-12-31',
                'defects_liability_period' => 180,
                'retention_percentage' => 5.00,
                'advance_payment_percentage' => 5.00,
                'payment_terms' => 'التكلفة الفعلية + هامش ربح 15%',
                'penalty_clause' => 'غرامة تأخير بنسبة 0.05% من قيمة العقد عن كل يوم تأخير',
                'scope_of_work' => 'توسعة المصنع الحالي بإضافة خطوط إنتاج جديدة ومستودعات',
                'contract_status' => 'active',
                'original_contract_value' => 30000000.00,
                'current_contract_value' => 30000000.00,
                'total_change_orders_value' => 0.00,
                'contract_manager_id' => $user->id,
                'project_manager_id' => $user->id,
                'gl_revenue_account_id' => $glAccountsCreated[1]->id,
                'gl_receivable_account_id' => $glAccountsCreated[0]->id,
                'company_id' => $company->id,
            ],
        ];

        $contractsCreated = [];
        foreach ($contracts as $contractData) {
            $contract = Contract::firstOrCreate(
                ['contract_code' => $contractData['contract_code'], 'company_id' => $company->id],
                $contractData
            );
            $contractsCreated[] = $contract;

            // Create sample clauses for first contract
            if ($contract->contract_code === 'CNT-2026-0001') {
                $this->command->info('Creating clauses for contract ' . $contract->contract_code);
                $clauses = [
                    [
                        'contract_id' => $contract->id,
                        'clause_number' => '1.1',
                        'clause_title' => 'مدة العقد',
                        'clause_content' => 'مدة العقد 700 يوم تبدأ من تاريخ إصدار أمر المباشرة',
                        'clause_category' => 'time',
                        'is_critical' => true,
                        'display_order' => 1,
                        'company_id' => $company->id,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'clause_number' => '2.1',
                        'clause_title' => 'شروط الدفع',
                        'clause_content' => 'يتم الدفع بموجب مستخلصات شهرية مع احتجاز 5% من قيمة كل مستخلص',
                        'clause_category' => 'payment',
                        'is_critical' => true,
                        'display_order' => 2,
                        'company_id' => $company->id,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'clause_number' => '3.1',
                        'clause_title' => 'الغرامات',
                        'clause_content' => 'في حالة التأخير عن الموعد المحدد يتم تطبيق غرامة تأخير',
                        'clause_category' => 'penalties',
                        'is_critical' => true,
                        'display_order' => 3,
                        'company_id' => $company->id,
                    ],
                ];

                foreach ($clauses as $clauseData) {
                    ContractClause::firstOrCreate(
                        ['contract_id' => $contract->id, 'clause_number' => $clauseData['clause_number']],
                        $clauseData
                    );
                }

                // Create sample milestones
                $this->command->info('Creating milestones for contract ' . $contract->contract_code);
                $milestones = [
                    [
                        'contract_id' => $contract->id,
                        'milestone_number' => 1,
                        'milestone_name' => 'أعمال الحفر والأساسات',
                        'description' => 'إنجاز أعمال الحفر وصب الأساسات',
                        'planned_date' => '2026-04-15',
                        'payment_percentage' => 15.00,
                        'payment_amount' => 7500000.00,
                        'status' => 'in_progress',
                        'completion_percentage' => 60.00,
                        'responsible_person_id' => $user->id,
                        'company_id' => $company->id,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'milestone_number' => 2,
                        'milestone_name' => 'الهيكل الإنشائي',
                        'description' => 'إنجاز الهيكل الإنشائي الخرساني',
                        'planned_date' => '2026-09-15',
                        'payment_percentage' => 35.00,
                        'payment_amount' => 17500000.00,
                        'status' => 'not_started',
                        'completion_percentage' => 0.00,
                        'responsible_person_id' => $user->id,
                        'company_id' => $company->id,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'milestone_number' => 3,
                        'milestone_name' => 'التشطيبات الداخلية',
                        'description' => 'إنجاز جميع أعمال التشطيبات الداخلية',
                        'planned_date' => '2027-06-30',
                        'payment_percentage' => 30.00,
                        'payment_amount' => 15000000.00,
                        'status' => 'not_started',
                        'completion_percentage' => 0.00,
                        'responsible_person_id' => $user->id,
                        'company_id' => $company->id,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'milestone_number' => 4,
                        'milestone_name' => 'التسليم النهائي',
                        'description' => 'التسليم النهائي للمشروع مع جميع الوثائق',
                        'planned_date' => '2027-12-31',
                        'payment_percentage' => 20.00,
                        'payment_amount' => 10000000.00,
                        'status' => 'not_started',
                        'completion_percentage' => 0.00,
                        'responsible_person_id' => $user->id,
                        'company_id' => $company->id,
                    ],
                ];

                foreach ($milestones as $milestoneData) {
                    ContractMilestone::firstOrCreate(
                        ['contract_id' => $contract->id, 'milestone_number' => $milestoneData['milestone_number']],
                        $milestoneData
                    );
                }
            }
        }

        $this->command->info('Contracts module seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . count($currencies) . ' currencies');
        $this->command->info('- ' . count($glAccounts) . ' GL accounts');
        $this->command->info('- ' . count($clients) . ' clients');
        $this->command->info('- ' . count($contracts) . ' contracts');
        $this->command->info('- 3 clauses for contract CNT-2026-0001');
        $this->command->info('- 4 milestones for contract CNT-2026-0001');
    }
}

