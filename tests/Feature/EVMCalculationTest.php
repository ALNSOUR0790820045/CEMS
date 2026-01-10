<?php

namespace Tests\Feature;

use App\Models\ActualCost;
use App\Models\Project;
use App\Models\ProjectBudget;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\CostCode;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EVMCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected Currency $currency;
    protected CostCode $costCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->currency = Currency::factory()->create();
        $this->costCode = CostCode::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    /**
     * Test EVM analysis calculates CPI correctly.
     */
    public function test_evm_calculates_cpi_correctly(): void
    {
        // Create budget
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'total_budget' => 1000000,
        ]);

        // Create actual costs
        $vendor = Vendor::factory()->create(['company_id' => $this->company->id]);
        
        ActualCost::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'amount_local' => 400000, // AC
            'company_id' => $this->company->id,
            'transaction_date' => now(),
            'posted_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'vendor_id' => $vendor->id,
        ]);

        // Get EVM analysis with 50% completion
        $response = $this->getJson("/api/project-cost-reports/evm-analysis/{$this->project->id}?percentage_complete=50");

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // EV = BAC * % Complete = 1000000 * 0.5 = 500000
        // AC = 400000
        // CPI = EV / AC = 500000 / 400000 = 1.25
        $this->assertEquals(1000000, $data['bac']);
        $this->assertEquals(500000, $data['earned_value']);
        $this->assertEquals(400000, $data['actual_cost']);
        $this->assertEquals(1.25, $data['cpi']);
    }

    /**
     * Test EVM calculates EAC correctly.
     */
    public function test_evm_calculates_eac_correctly(): void
    {
        // Create budget
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'total_budget' => 1000000,
        ]);

        // Create actual costs representing cost overrun
        $vendor = Vendor::factory()->create(['company_id' => $this->company->id]);
        
        ActualCost::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'amount_local' => 600000, // AC at 50% complete (overrun)
            'company_id' => $this->company->id,
            'transaction_date' => now(),
            'posted_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'vendor_id' => $vendor->id,
        ]);

        // Get EVM analysis
        $response = $this->getJson("/api/project-cost-reports/evm-analysis/{$this->project->id}?percentage_complete=50");

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // EV = 1000000 * 0.5 = 500000
        // AC = 600000
        // CPI = 500000 / 600000 = 0.8333
        // EAC = BAC / CPI = 1000000 / 0.8333 = 1200000
        // VAC = BAC - EAC = 1000000 - 1200000 = -200000
        
        $this->assertEqualsWithDelta(0.8333, $data['cpi'], 0.01);
        $this->assertEqualsWithDelta(1200000, $data['eac'], 1000);
        $this->assertEqualsWithDelta(-200000, $data['vac'], 1000);
    }

    /**
     * Test cost variance calculation.
     */
    public function test_calculates_cost_variance(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'total_budget' => 1000000,
        ]);

        $vendor = Vendor::factory()->create(['company_id' => $this->company->id]);
        
        ActualCost::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'amount_local' => 300000,
            'company_id' => $this->company->id,
            'transaction_date' => now(),
            'posted_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'vendor_id' => $vendor->id,
        ]);

        $response = $this->getJson("/api/project-cost-reports/evm-analysis/{$this->project->id}?percentage_complete=40");

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // EV = 1000000 * 0.4 = 400000
        // AC = 300000
        // CV = EV - AC = 400000 - 300000 = 100000 (favorable)
        
        $this->assertEquals(100000, $data['cost_variance']);
    }

    /**
     * Test TCPI calculation for project completion.
     */
    public function test_calculates_tcpi(): void
    {
        $budget = ProjectBudget::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'total_budget' => 1000000,
        ]);

        $vendor = Vendor::factory()->create(['company_id' => $this->company->id]);
        
        ActualCost::factory()->create([
            'project_id' => $this->project->id,
            'cost_code_id' => $this->costCode->id,
            'amount_local' => 500000,
            'company_id' => $this->company->id,
            'transaction_date' => now(),
            'posted_by_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'vendor_id' => $vendor->id,
        ]);

        $response = $this->getJson("/api/project-cost-reports/evm-analysis/{$this->project->id}?percentage_complete=40");

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // EV = 1000000 * 0.4 = 400000
        // AC = 500000
        // Work Remaining = BAC - EV = 1000000 - 400000 = 600000
        // Funds Remaining = BAC - AC = 1000000 - 500000 = 500000
        // TCPI = 600000 / 500000 = 1.2
        
        $this->assertEqualsWithDelta(1.2, $data['tcpi'], 0.01);
    }
}
