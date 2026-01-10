<?php

namespace Tests\Feature;

use App\Models\ComplianceRequirement;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ComplianceRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_compliance_requirements(): void
    {
        ComplianceRequirement::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/compliance-requirements');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'code', 'name', 'category', 'frequency']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_compliance_requirement(): void
    {
        $requirementData = [
            'code' => 'COMP-TEST-001',
            'name' => 'Safety Inspection',
            'name_en' => 'Safety Inspection EN',
            'description' => 'Monthly safety equipment inspection',
            'category' => 'safety',
            'regulation_reference' => 'REG-2024-001',
            'is_mandatory' => true,
            'frequency' => 'monthly',
            'responsible_role' => 'Safety Officer',
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/compliance-requirements', $requirementData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'code', 'name']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'code' => 'COMP-TEST-001',
                         'name' => 'Safety Inspection',
                     ]
                 ]);

        $this->assertDatabaseHas('compliance_requirements', [
            'code' => 'COMP-TEST-001',
        ]);
    }

    public function test_can_update_compliance_requirement(): void
    {
        $requirement = ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $updateData = [
            'name' => 'Updated Requirement',
            'frequency' => 'quarterly',
        ];

        $response = $this->putJson("/api/compliance-requirements/{$requirement->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Requirement',
                         'frequency' => 'quarterly',
                     ]
                 ]);

        $this->assertDatabaseHas('compliance_requirements', [
            'id' => $requirement->id,
            'name' => 'Updated Requirement',
        ]);
    }

    public function test_can_delete_compliance_requirement(): void
    {
        $requirement = ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/compliance-requirements/{$requirement->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('compliance_requirements', [
            'id' => $requirement->id,
            'deleted_at' => null,
        ]);
    }

    public function test_can_filter_by_category(): void
    {
        ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
            'category' => 'safety',
        ]);

        ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
            'category' => 'environmental',
        ]);

        $response = $this->getJson("/api/compliance-requirements?company_id={$this->company->id}&category=safety");

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertGreaterThanOrEqual(1, count($data));
        $this->assertEquals('safety', $data[0]['category']);
    }

    public function test_can_filter_mandatory_requirements(): void
    {
        ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
            'is_mandatory' => true,
        ]);

        ComplianceRequirement::factory()->create([
            'company_id' => $this->company->id,
            'is_mandatory' => false,
        ]);

        $response = $this->getJson("/api/compliance-requirements?company_id={$this->company->id}&is_mandatory=1");

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertGreaterThanOrEqual(1, count($data));
        $this->assertTrue($data[0]['is_mandatory']);
    }
}
