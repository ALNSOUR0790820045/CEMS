<?php

namespace Tests\Feature;

use App\Models\ComplianceCheck;
use App\Models\ComplianceRequirement;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ComplianceCheckTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected ComplianceRequirement $requirement;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->requirement = ComplianceRequirement::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_compliance_checks(): void
    {
        ComplianceCheck::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'compliance_requirement_id' => $this->requirement->id,
        ]);

        $response = $this->getJson('/api/compliance-checks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'check_number', 'status', 'due_date']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_compliance_check(): void
    {
        $checkData = [
            'compliance_requirement_id' => $this->requirement->id,
            'check_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/compliance-checks', $checkData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'check_number', 'status']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'pending',
                     ]
                 ]);

        $this->assertDatabaseHas('compliance_checks', [
            'compliance_requirement_id' => $this->requirement->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_mark_check_as_passed(): void
    {
        $check = ComplianceCheck::factory()->create([
            'company_id' => $this->company->id,
            'compliance_requirement_id' => $this->requirement->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/compliance-checks/{$check->id}/pass", [
            'findings' => 'All requirements met',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'passed',
                     ]
                 ]);

        $this->assertDatabaseHas('compliance_checks', [
            'id' => $check->id,
            'status' => 'passed',
        ]);
    }

    public function test_can_mark_check_as_failed(): void
    {
        $check = ComplianceCheck::factory()->create([
            'company_id' => $this->company->id,
            'compliance_requirement_id' => $this->requirement->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/compliance-checks/{$check->id}/fail", [
            'findings' => 'Safety equipment missing',
            'corrective_action' => 'Purchase and install equipment',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'failed',
                     ]
                 ]);

        $this->assertDatabaseHas('compliance_checks', [
            'id' => $check->id,
            'status' => 'failed',
            'findings' => 'Safety equipment missing',
        ]);
    }

    public function test_can_filter_overdue_checks(): void
    {
        ComplianceCheck::factory()->create([
            'company_id' => $this->company->id,
            'compliance_requirement_id' => $this->requirement->id,
            'due_date' => now()->subDays(5),
            'status' => 'pending',
        ]);

        ComplianceCheck::factory()->create([
            'company_id' => $this->company->id,
            'compliance_requirement_id' => $this->requirement->id,
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/compliance-checks?company_id={$this->company->id}&overdue=1");

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data.data')));
    }
}
