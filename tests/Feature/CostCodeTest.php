<?php

namespace Tests\Feature;

use App\Models\CostCode;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CostCodeTest extends TestCase
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

    /**
     * Test can create a cost code.
     */
    public function test_can_create_cost_code(): void
    {
        $costCodeData = [
            'code' => '1000',
            'name' => 'Direct Labor',
            'name_en' => 'Direct Labor',
            'cost_type' => 'direct',
            'cost_category' => 'labor',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/cost-codes', $costCodeData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'code',
                     'name',
                     'cost_type',
                     'level',
                 ]);

        $this->assertDatabaseHas('cost_codes', [
            'code' => '1000',
            'name' => 'Direct Labor',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test can create hierarchical cost codes.
     */
    public function test_can_create_hierarchical_cost_codes(): void
    {
        $parent = CostCode::factory()->create([
            'company_id' => $this->company->id,
            'code' => '1000',
            'level' => 1,
        ]);

        $childData = [
            'code' => '1100',
            'name' => 'Site Labor',
            'cost_type' => 'direct',
            'cost_category' => 'labor',
            'parent_id' => $parent->id,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/cost-codes', $childData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'level' => 2,
                     'parent_id' => $parent->id,
                 ]);
    }

    /**
     * Test can get cost codes tree.
     */
    public function test_can_get_cost_codes_tree(): void
    {
        $parent = CostCode::factory()->create([
            'company_id' => $this->company->id,
            'parent_id' => null,
            'level' => 1,
        ]);

        CostCode::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'parent_id' => $parent->id,
            'level' => 2,
        ]);

        $response = $this->getJson('/api/cost-codes/tree');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'code',
                         'name',
                         'children',
                     ]
                 ]);
    }

    /**
     * Test cannot delete cost code with children.
     */
    public function test_cannot_delete_cost_code_with_children(): void
    {
        $parent = CostCode::factory()->create([
            'company_id' => $this->company->id,
        ]);

        CostCode::factory()->create([
            'company_id' => $this->company->id,
            'parent_id' => $parent->id,
        ]);

        $response = $this->deleteJson("/api/cost-codes/{$parent->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'error' => 'Cannot delete cost code with children'
                 ]);
    }
}
