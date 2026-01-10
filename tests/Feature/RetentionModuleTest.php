<?php

namespace Tests\Feature;

use App\Models\Retention;
use App\Models\RetentionRelease;
use App\Models\AdvancePayment;
use App\Models\DefectsLiability;
use App\Models\DefectNotification;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RetentionModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected Contract $contract;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->create();
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->contract = Contract::factory()->create(['project_id' => $this->project->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    /**
     * Test can create a retention.
     */
    public function test_can_create_retention(): void
    {
        $retentionData = [
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'retention_type' => 'performance',
            'retention_percentage' => 10.00,
            'max_retention_percentage' => 10.00,
            'release_schedule' => 'staged',
            'total_contract_value' => 1000000.00,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/retentions', $retentionData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'retention_number', 'project_id', 'contract_id']
                 ])
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('retentions', [
            'project_id' => $this->project->id,
            'retention_type' => 'performance',
        ]);
    }

    /**
     * Test retention accumulation from progress bill.
     */
    public function test_can_calculate_retention_from_bill(): void
    {
        $retention = Retention::factory()->create([
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'retention_percentage' => 10.00,
            'max_retention_percentage' => 10.00,
            'total_contract_value' => 1000000.00,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $billData = [
            'bill_amount' => 100000.00,
            'bill_date' => now()->toDateString(),
        ];

        $response = $this->postJson("/api/retentions/{$retention->id}/calculate", $billData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('retention_accumulations', [
            'retention_id' => $retention->id,
            'bill_amount' => 100000.00,
        ]);

        // Verify retention was updated
        $retention->refresh();
        $this->assertEquals(10000.00, $retention->total_retention_amount);
        $this->assertEquals(10000.00, $retention->balance_amount);
    }

    /**
     * Test partial release of retention.
     */
    public function test_can_create_partial_release(): void
    {
        $retention = Retention::factory()->create([
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'total_retention_amount' => 50000.00,
            'balance_amount' => 50000.00,
        ]);

        $releaseData = [
            'retention_id' => $retention->id,
            'release_type' => 'partial',
            'release_date' => now()->toDateString(),
            'release_amount' => 25000.00,
            'release_percentage' => 50.00,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/retention-releases', $releaseData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('retention_releases', [
            'retention_id' => $retention->id,
            'release_amount' => 25000.00,
            'status' => 'pending',
        ]);
    }

    /**
     * Test retention release approval and execution.
     */
    public function test_can_approve_and_release_retention(): void
    {
        $retention = Retention::factory()->create([
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
            'total_retention_amount' => 50000.00,
            'balance_amount' => 50000.00,
        ]);

        $release = RetentionRelease::create([
            'retention_id' => $retention->id,
            'release_type' => 'partial',
            'release_date' => now()->toDateString(),
            'release_amount' => 25000.00,
            'release_percentage' => 50.00,
            'remaining_balance' => 25000.00,
            'status' => 'pending',
            'company_id' => $this->company->id,
        ]);

        // Test approval
        $approveResponse = $this->postJson("/api/retention-releases/{$release->id}/approve");
        $approveResponse->assertStatus(200)
                       ->assertJson(['success' => true]);

        $release->refresh();
        $this->assertEquals('approved', $release->status);
        $this->assertEquals($this->user->id, $release->approved_by_id);

        // Test release
        $releaseResponse = $this->postJson("/api/retention-releases/{$release->id}/release");
        $releaseResponse->assertStatus(200)
                       ->assertJson(['success' => true]);

        $release->refresh();
        $retention->refresh();
        
        $this->assertEquals('released', $release->status);
        $this->assertEquals(25000.00, $retention->released_amount);
        $this->assertEquals(25000.00, $retention->balance_amount);
    }

    /**
     * Test advance payment creation.
     */
    public function test_can_create_advance_payment(): void
    {
        $advanceData = [
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'advance_type' => 'mobilization',
            'advance_percentage' => 15.00,
            'advance_amount' => 150000.00,
            'currency_id' => $this->currency->id,
            'guarantee_required' => true,
            'recovery_start_percentage' => 10.00,
            'recovery_percentage' => 10.00,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/advance-payments', $advanceData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('advance_payments', [
            'project_id' => $this->project->id,
            'advance_amount' => 150000.00,
            'balance_amount' => 150000.00,
        ]);
    }

    /**
     * Test defects liability period creation.
     */
    public function test_can_create_defects_liability_period(): void
    {
        $dlpData = [
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'taking_over_date' => now()->toDateString(),
            'dlp_months' => 12,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/defects-liability', $dlpData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('defects_liability', [
            'project_id' => $this->project->id,
            'dlp_months' => 12,
            'status' => 'active',
        ]);
    }

    /**
     * Test defect notification during liability period.
     */
    public function test_can_record_defect_notification(): void
    {
        $dlp = DefectsLiability::factory()->create([
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'company_id' => $this->company->id,
        ]);

        $defectData = [
            'defects_liability_id' => $dlp->id,
            'notification_date' => now()->toDateString(),
            'notified_by' => 'Engineer A',
            'defect_description' => 'Crack in concrete wall',
            'location' => 'Building A, Floor 2',
            'severity' => 'major',
            'rectification_deadline' => now()->addDays(30)->toDateString(),
        ];

        $response = $this->postJson('/api/defect-notifications', $defectData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('defect_notifications', [
            'defects_liability_id' => $dlp->id,
            'severity' => 'major',
            'status' => 'notified',
        ]);

        // Verify DLP defects count was incremented
        $dlp->refresh();
        $this->assertEquals(1, $dlp->defects_reported);
    }

    /**
     * Test retention summary report.
     */
    public function test_can_get_retention_summary(): void
    {
        Retention::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'currency_id' => $this->currency->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/reports/retention-summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_retentions',
                         'total_retention_amount',
                         'total_released_amount',
                         'total_balance_amount',
                         'by_status',
                         'by_type',
                     ]
                 ]);
    }
}
