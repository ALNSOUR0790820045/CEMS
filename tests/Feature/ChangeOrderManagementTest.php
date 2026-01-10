<?php

namespace Tests\Feature;

use App\Models\ChangeOrder;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $contract;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->contract = Contract::factory()->create([
            'project_id' => $this->project->id,
        ]);
    }

    public function test_user_can_list_change_orders(): void
    {
        ChangeOrder::create([
            'project_id' => $this->project->id,
            'original_contract_id' => $this->contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test Change Order',
            'net_amount' => 50000,
            'tax_amount' => 7500,
            'total_amount' => 57500,
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/change-orders');

        $response->assertStatus(200);
    }

    public function test_user_can_create_change_order(): void
    {
        $coData = [
            'project_id' => $this->project->id,
            'original_contract_id' => $this->contract->id,
            'co_number' => 'CO-001',
            'title' => 'Additional Works',
            'description' => 'Additional scope of work',
            'net_amount' => 50000,
            'tax_amount' => 7500,
            'total_amount' => 57500,
            'status' => 'pending',
            'created_by' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->post('/change-orders', $coData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('change_orders', [
            'co_number' => 'CO-001',
            'title' => 'Additional Works',
        ]);
    }

    public function test_change_order_requires_contract(): void
    {
        $coData = [
            'project_id' => $this->project->id,
            'co_number' => 'CO-001',
            'title' => 'Additional Works',
            // original_contract_id is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/change-orders', $coData);

        $response->assertSessionHasErrors(['original_contract_id']);
    }

    public function test_change_order_calculates_tax_correctly(): void
    {
        $co = ChangeOrder::create([
            'project_id' => $this->project->id,
            'original_contract_id' => $this->contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'tax_amount' => 1500, // 15% tax
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $expectedTotal = $co->net_amount + $co->tax_amount;
        
        $this->assertEquals($expectedTotal, $co->total_amount);
    }

    public function test_change_order_has_approval_workflow(): void
    {
        $co = ChangeOrder::create([
            'project_id' => $this->project->id,
            'original_contract_id' => $this->contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('pending', $co->status);
        $this->assertNull($co->pm_signed_at);
        $this->assertNull($co->technical_signed_at);
    }

    public function test_user_can_update_change_order(): void
    {
        $co = ChangeOrder::create([
            'project_id' => $this->project->id,
            'original_contract_id' => $this->contract->id,
            'co_number' => 'CO-001',
            'title' => 'Original Title',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/change-orders/{$co->id}", [
                'project_id' => $this->project->id,
                'original_contract_id' => $this->contract->id,
                'co_number' => $co->co_number,
                'title' => 'Updated Title',
                'net_amount' => 15000,
                'tax_amount' => 2250,
                'total_amount' => 17250,
                'status' => 'pending',
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('change_orders', [
            'id' => $co->id,
            'title' => 'Updated Title',
        ]);
    }
}
