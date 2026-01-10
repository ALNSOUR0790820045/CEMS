<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\CostCenter;
use App\Models\CostCategory;
use App\Models\CostAllocation;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Currency;
use App\Models\GLAccount;
use App\Models\Project;

class CostAccountingTest extends TestCase
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
    }

    public function test_can_create_cost_center()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-centers', [
                'code' => 'CC001',
                'name' => 'مركز التكلفة الأول',
                'name_en' => 'First Cost Center',
                'description' => 'Test cost center',
                'type' => 'project',
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'CC001',
                'name' => 'مركز التكلفة الأول',
                'type' => 'project',
            ]);

        $this->assertDatabaseHas('cost_centers', [
            'code' => 'CC001',
            'name' => 'مركز التكلفة الأول',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_cost_category()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-categories', [
                'code' => 'CAT001',
                'name' => 'مواد مباشرة',
                'name_en' => 'Direct Materials',
                'type' => 'direct_material',
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'CAT001',
                'name' => 'مواد مباشرة',
                'type' => 'direct_material',
            ]);

        $this->assertDatabaseHas('cost_categories', [
            'code' => 'CAT001',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_create_and_approve_budget()
    {
        // Create dependencies
        $costCenter = CostCenter::create([
            'code' => 'CC001',
            'name' => 'Test Cost Center',
            'type' => 'project',
            'company_id' => $this->company->id,
        ]);

        $costCategory = CostCategory::create([
            'code' => 'CAT001',
            'name' => 'Direct Materials',
            'type' => 'direct_material',
            'company_id' => $this->company->id,
        ]);

        // Create budget
        $response = $this->actingAs($this->user)
            ->postJson('/api/budgets', [
                'budget_name' => 'Annual Budget 2026',
                'fiscal_year' => 2026,
                'budget_type' => 'annual',
                'cost_center_id' => $costCenter->id,
                'total_amount' => 100000,
                'items' => [
                    [
                        'cost_category_id' => $costCategory->id,
                        'budgeted_amount' => 50000,
                        'notes' => 'Q1 Budget',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'budget_number',
                'budget_name',
                'status',
                'items',
            ]);

        $budgetId = $response->json('id');

        // Approve budget
        $approveResponse = $this->actingAs($this->user)
            ->postJson("/api/budgets/{$budgetId}/approve");

        $approveResponse->assertStatus(200)
            ->assertJson([
                'status' => 'approved',
            ]);

        $this->assertDatabaseHas('budgets', [
            'id' => $budgetId,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_can_create_and_post_cost_allocation()
    {
        // Create dependencies
        $costCenter = CostCenter::create([
            'code' => 'CC001',
            'name' => 'Test Cost Center',
            'type' => 'project',
            'company_id' => $this->company->id,
        ]);

        $costCategory = CostCategory::create([
            'code' => 'CAT001',
            'name' => 'Direct Materials',
            'type' => 'direct_material',
            'company_id' => $this->company->id,
        ]);

        $currency = Currency::create([
            'code' => 'SAR',
            'name' => 'Saudi Riyal',
            'symbol' => 'ر.س',
            'exchange_rate' => 1.0000,
            'is_base' => true,
            'company_id' => $this->company->id,
        ]);

        $glAccount = GLAccount::create([
            'account_code' => '5000',
            'account_name' => 'Cost of Materials',
            'account_type' => 'expense',
            'allow_posting' => true,
            'company_id' => $this->company->id,
        ]);

        // Create allocation
        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-allocations', [
                'allocation_date' => '2026-01-07',
                'cost_center_id' => $costCenter->id,
                'cost_category_id' => $costCategory->id,
                'gl_account_id' => $glAccount->id,
                'amount' => 5000,
                'currency_id' => $currency->id,
                'exchange_rate' => 1.0000,
                'description' => 'Test allocation',
                'reference_type' => 'manual',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'allocation_number',
                'status',
            ]);

        $allocationId = $response->json('id');

        // Post allocation
        $postResponse = $this->actingAs($this->user)
            ->postJson("/api/cost-allocations/{$allocationId}/post");

        $postResponse->assertStatus(200)
            ->assertJson([
                'status' => 'posted',
            ]);

        $this->assertDatabaseHas('cost_allocations', [
            'id' => $allocationId,
            'status' => 'posted',
            'posted_by_id' => $this->user->id,
        ]);
    }

    public function test_budget_variance_calculation()
    {
        // Create dependencies
        $costCenter = CostCenter::create([
            'code' => 'CC001',
            'name' => 'Test Cost Center',
            'type' => 'project',
            'company_id' => $this->company->id,
        ]);

        $costCategory = CostCategory::create([
            'code' => 'CAT001',
            'name' => 'Direct Materials',
            'type' => 'direct_material',
            'company_id' => $this->company->id,
        ]);

        $currency = Currency::create([
            'code' => 'SAR',
            'name' => 'Saudi Riyal',
            'symbol' => 'ر.س',
            'exchange_rate' => 1.0000,
            'is_base' => true,
            'company_id' => $this->company->id,
        ]);

        $glAccount = GLAccount::create([
            'account_code' => '5000',
            'account_name' => 'Cost of Materials',
            'account_type' => 'expense',
            'allow_posting' => true,
            'company_id' => $this->company->id,
        ]);

        // Create budget
        $budget = Budget::create([
            'budget_number' => 'BDG-2026-0001',
            'budget_name' => 'Test Budget',
            'fiscal_year' => 2026,
            'budget_type' => 'annual',
            'cost_center_id' => $costCenter->id,
            'total_amount' => 100000,
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        BudgetItem::create([
            'budget_id' => $budget->id,
            'cost_category_id' => $costCategory->id,
            'gl_account_id' => $glAccount->id,
            'budgeted_amount' => 50000,
        ]);

        // Create allocation
        $allocation = CostAllocation::create([
            'allocation_number' => 'CA-2026-0001',
            'allocation_date' => '2026-01-07',
            'cost_center_id' => $costCenter->id,
            'cost_category_id' => $costCategory->id,
            'gl_account_id' => $glAccount->id,
            'amount' => 30000,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0000,
            'status' => 'posted',
            'company_id' => $this->company->id,
        ]);

        // Get budget variance report
        $response = $this->actingAs($this->user)
            ->getJson("/api/reports/budget-variance?budget_id={$budget->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'budget',
                'items',
                'summary',
            ]);

        $summary = $response->json('summary');
        $this->assertEquals(50000, $summary['total_budgeted']);
        $this->assertEquals(30000, $summary['total_actual']);
        $this->assertEquals(20000, $summary['total_variance']);
    }

    public function test_cannot_update_approved_budget()
    {
        $costCenter = CostCenter::create([
            'code' => 'CC001',
            'name' => 'Test Cost Center',
            'type' => 'project',
            'company_id' => $this->company->id,
        ]);

        $budget = Budget::create([
            'budget_number' => 'BDG-2026-0001',
            'budget_name' => 'Test Budget',
            'fiscal_year' => 2026,
            'budget_type' => 'annual',
            'cost_center_id' => $costCenter->id,
            'total_amount' => 100000,
            'status' => 'approved',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/budgets/{$budget->id}", [
                'budget_name' => 'Updated Budget',
                'fiscal_year' => 2026,
                'budget_type' => 'annual',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Cannot update approved budget',
            ]);
    }

    public function test_cannot_delete_posted_cost_allocation()
    {
        $costCenter = CostCenter::create([
            'code' => 'CC001',
            'name' => 'Test Cost Center',
            'type' => 'project',
            'company_id' => $this->company->id,
        ]);

        $costCategory = CostCategory::create([
            'code' => 'CAT001',
            'name' => 'Direct Materials',
            'type' => 'direct_material',
            'company_id' => $this->company->id,
        ]);

        $currency = Currency::create([
            'code' => 'SAR',
            'name' => 'Saudi Riyal',
            'symbol' => 'ر.س',
            'exchange_rate' => 1.0000,
            'is_base' => true,
            'company_id' => $this->company->id,
        ]);

        $glAccount = GLAccount::create([
            'account_code' => '5000',
            'account_name' => 'Cost of Materials',
            'account_type' => 'expense',
            'allow_posting' => true,
            'company_id' => $this->company->id,
        ]);

        $allocation = CostAllocation::create([
            'allocation_number' => 'CA-2026-0001',
            'allocation_date' => '2026-01-07',
            'cost_center_id' => $costCenter->id,
            'cost_category_id' => $costCategory->id,
            'gl_account_id' => $glAccount->id,
            'amount' => 5000,
            'currency_id' => $currency->id,
            'exchange_rate' => 1.0000,
            'status' => 'posted',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/cost-allocations/{$allocation->id}");

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Cannot delete posted allocation',
            ]);
    }
}
