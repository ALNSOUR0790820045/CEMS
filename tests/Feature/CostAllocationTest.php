<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CostCenter;
use App\Models\Currency;
use App\Models\GlAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostAllocationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::create([
            'name' => 'Test Company',
            'name_en' => 'Test Company',
            'slug' => 'test-company',
            'country' => 'US',
            'is_active' => true,
        ]);
        
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_can_create_cost_allocation(): void
    {
        $costCenter = CostCenter::create([
            'code' => 'CC-001',
            'name' => 'Marketing',
            'type' => 'department',
            'company_id' => $this->company->id,
        ]);

        $glAccount = GlAccount::create([
            'code' => 'GL-001',
            'name' => 'Office Supplies',
            'type' => 'expense',
            'company_id' => $this->company->id,
        ]);

        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
        ]);

        $data = [
            'transaction_date' => '2024-01-15',
            'source_type' => 'Project',
            'source_id' => 1,
            'cost_center_id' => $costCenter->id,
            'gl_account_id' => $glAccount->id,
            'amount' => 1500.00,
            'currency_id' => $currency->id,
            'description' => 'Office supplies purchase',
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-allocations', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'transaction_date', 'amount', 'description']
            ]);

        $this->assertDatabaseHas('cost_allocations', ['amount' => 1500.00]);
    }

    public function test_validation_fails_for_invalid_cost_allocation_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-allocations', []);

        $response->assertStatus(422);
    }
}
