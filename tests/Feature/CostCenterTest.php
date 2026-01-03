<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostCenterTest extends TestCase
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

    public function test_can_create_cost_center(): void
    {
        $data = [
            'code' => 'CC-001',
            'name' => 'Marketing Department',
            'type' => 'department',
            'is_active' => true,
            'company_id' => $this->company->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-centers', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'code', 'name', 'type', 'is_active', 'company_id']
            ]);

        $this->assertDatabaseHas('cost_centers', ['code' => 'CC-001']);
    }

    public function test_validation_fails_for_invalid_cost_center_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/cost-centers', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name', 'type', 'company_id']);
    }
}
