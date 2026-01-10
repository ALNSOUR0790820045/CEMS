<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\Risk;
use App\Models\RiskRegister;
use App\Models\RiskCategory;
use App\Models\RiskAssessment;
use App\Models\RiskResponse;
use App\Models\RiskMonitoring;
use App\Models\RiskIncident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RiskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_risk_register(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'name' => 'Project Risk Register',
            'description' => 'Main risk register for the project',
            'review_frequency' => 'monthly',
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/risk-registers', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Project Risk Register',
                         'status' => 'draft',
                     ]
                 ]);

        $this->assertDatabaseHas('risk_registers', [
            'name' => 'Project Risk Register',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_risk_register_auto_generates_number(): void
    {
        $register = RiskRegister::create([
            'project_id' => $this->project->id,
            'name' => 'Test Register',
            'prepared_by_id' => $this->user->id,
            'review_frequency' => 'monthly',
            'company_id' => $this->company->id,
        ]);

        $this->assertNotNull($register->register_number);
        $this->assertStringStartsWith('RR-', $register->register_number);
    }

    public function test_can_create_risk(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'title' => 'Material Shortage Risk',
            'description' => 'Risk of material shortage affecting timeline',
            'category' => 'resource',
            'identification_date' => now()->format('Y-m-d'),
            'probability' => 'high',
            'probability_score' => 4,
            'impact' => 'high',
            'impact_score' => 4,
            'cost_impact_expected' => 50000,
            'schedule_impact_days' => 15,
            'response_strategy' => 'mitigate',
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/risks', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'title' => 'Material Shortage Risk',
                         'risk_score' => 16,
                         'risk_level' => 'critical',
                     ]
                 ]);
    }

    public function test_risk_auto_calculates_score_and_level(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'title' => 'Test Risk',
            'description' => 'Test',
            'category' => 'technical',
            'identification_date' => now(),
            'identified_by_id' => $this->user->id,
            'probability' => 'high',
            'probability_score' => 4,
            'impact' => 'medium',
            'impact_score' => 3,
            'company_id' => $this->company->id,
        ]);

        $this->assertEquals(12, $risk->risk_score);
        $this->assertEquals('high', $risk->risk_level);
    }

    public function test_can_assess_risk(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::factory()->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'assessment_date' => now()->format('Y-m-d'),
            'assessment_type' => 'reassessment',
            'probability' => 'medium',
            'probability_score' => 3,
            'impact' => 'high',
            'impact_score' => 4,
            'cost_impact' => 30000,
            'schedule_impact' => 10,
            'justification' => 'Risk has been partially mitigated',
        ];

        $response = $this->postJson("/api/risks/{$risk->id}/assess", $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('risk_assessments', [
            'risk_id' => $risk->id,
            'assessment_type' => 'reassessment',
        ]);
    }

    public function test_can_add_risk_response(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::factory()->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'response_number' => 'RESP-001',
            'response_type' => 'preventive',
            'strategy' => 'mitigate',
            'description' => 'Secure alternative suppliers',
            'action_required' => 'Contact 3 additional suppliers',
            'target_date' => now()->addDays(30)->format('Y-m-d'),
            'cost_of_response' => 5000,
        ];

        $response = $this->postJson("/api/risks/{$risk->id}/respond", $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('risk_responses', [
            'risk_id' => $risk->id,
            'response_number' => 'RESP-001',
        ]);
    }

    public function test_can_monitor_risk(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::factory()->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'monitoring_date' => now()->format('Y-m-d'),
            'current_status' => 'Under control',
            'probability_change' => 'decreased',
            'impact_change' => 'same',
            'trigger_status' => 'not_triggered',
            'actions_taken' => 'Implemented mitigation measures',
            'next_review_date' => now()->addDays(14)->format('Y-m-d'),
        ];

        $response = $this->postJson("/api/risks/{$risk->id}/monitor", $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('risk_monitoring', [
            'risk_id' => $risk->id,
            'trigger_status' => 'not_triggered',
        ]);
    }

    public function test_can_close_risk(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::factory()->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'closure_reason' => 'Risk successfully mitigated',
            'lessons_learned' => 'Early engagement with suppliers is key',
        ];

        $response = $this->postJson("/api/risks/{$risk->id}/close", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $risk->refresh();
        $this->assertEquals('closed', $risk->status);
        $this->assertNotNull($risk->closed_date);
    }

    public function test_can_create_risk_incident(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $risk = Risk::factory()->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $data = [
            'risk_id' => $risk->id,
            'project_id' => $this->project->id,
            'incident_date' => now()->format('Y-m-d'),
            'title' => 'Material Delivery Delay',
            'description' => 'Supplier failed to deliver on time',
            'category' => 'resource',
            'actual_cost_impact' => 25000,
            'actual_schedule_impact' => 7,
            'immediate_actions' => 'Found alternative supplier',
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/risk-incidents', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('risk_incidents', [
            'risk_id' => $risk->id,
            'title' => 'Material Delivery Delay',
        ]);
    }

    public function test_can_get_risk_summary_report(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        Risk::factory()->count(3)->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
            'risk_level' => 'high',
        ]);

        $response = $this->getJson("/api/reports/risk-summary/{$this->project->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_risks',
                         'critical_risks',
                         'high_risks',
                         'medium_risks',
                         'low_risks',
                     ]
                 ]);
    }

    public function test_can_get_top_risks(): void
    {
        $register = RiskRegister::factory()->create([
            'project_id' => $this->project->id,
            'prepared_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        Risk::factory()->count(5)->create([
            'risk_register_id' => $register->id,
            'project_id' => $this->project->id,
            'identified_by_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/reports/top-risks/{$this->project->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'title', 'risk_score', 'risk_level']
                     ]
                 ]);
    }
}
