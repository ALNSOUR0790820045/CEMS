<?php

namespace Tests\Unit;

use App\Models\ChangeOrder;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeOrderUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_total_amount_correctly(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $co = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $expectedTotal = 10000 + 1500;
        $this->assertEquals($expectedTotal, $co->total_amount);
    }

    public function test_generates_unique_co_number(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $co1 = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO 1',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $co2 = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-002',
            'title' => 'Test CO 2',
            'net_amount' => 20000,
            'tax_amount' => 3000,
            'total_amount' => 23000,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $this->assertNotEquals($co1->co_number, $co2->co_number);
    }

    public function test_belongs_to_contract(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $co = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $this->assertInstanceOf(Contract::class, $co->originalContract);
        $this->assertEquals($contract->id, $co->original_contract_id);
    }

    public function test_has_approval_fields(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $co = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $this->assertNull($co->pm_signed_at);
        $this->assertNull($co->technical_signed_at);
        $this->assertNull($co->consultant_signed_at);
        $this->assertNull($co->client_signed_at);
    }

    public function test_can_calculate_fees(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $contract = Contract::factory()->create(['project_id' => $project->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $co = ChangeOrder::create([
            'project_id' => $project->id,
            'original_contract_id' => $contract->id,
            'co_number' => 'CO-001',
            'title' => 'Test CO',
            'net_amount' => 10000,
            'fee_percentage' => 5, // 5%
            'calculated_fee' => 500,
            'stamp_duty' => 50,
            'total_fees' => 550,
            'tax_amount' => 1500,
            'total_amount' => 11500,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $this->assertEquals(500, $co->calculated_fee);
        $this->assertEquals(550, $co->total_fees);
    }
}
