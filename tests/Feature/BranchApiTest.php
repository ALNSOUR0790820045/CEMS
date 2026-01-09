<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BranchApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        // Authenticate user for API requests
        Sanctum::actingAs($this->user);
    }

    /**
     * Test can list all branches.
     */
    public function test_can_list_branches(): void
    {
        Branch::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/branches');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'code', 'name', 'company_id', 'is_active']
                         ]
                     ]
                 ]);
    }

    /**
     * Test can filter branches by company.
     */
    public function test_can_filter_branches_by_company(): void
    {
        $otherCompany = Company::factory()->create();
        
        Branch::factory()->count(2)->create(['company_id' => $this->company->id]);
        Branch::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->getJson('/api/branches?company_id=' . $this->company->id);

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data.data')));
    }

    /**
     * Test can create a branch.
     */
    public function test_can_create_branch(): void
    {
        $branchData = [
            'company_id' => $this->company->id,
            'code' => 'TEST-001',
            'name' => 'Test Branch',
            'name_en' => 'Test Branch EN',
            'city' => 'Amman',
            'country' => 'JO',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/branches', $branchData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'code', 'name']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'code' => 'TEST-001',
                         'name' => 'Test Branch',
                     ]
                 ]);

        $this->assertDatabaseHas('branches', ['code' => 'TEST-001']);
    }

    /**
     * Test branch creation validation.
     */
    public function test_branch_creation_requires_validation(): void
    {
        $response = $this->postJson('/api/branches', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['company_id', 'code', 'name']);
    }

    /**
     * Test branch code must be unique.
     */
    public function test_branch_code_must_be_unique(): void
    {
        Branch::factory()->create(['code' => 'UNIQUE-001']);

        $response = $this->postJson('/api/branches', [
            'company_id' => $this->company->id,
            'code' => 'UNIQUE-001',
            'name' => 'Test Branch',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code']);
    }

    /**
     * Test can show a specific branch.
     */
    public function test_can_show_branch(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/branches/' . $branch->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $branch->id,
                         'code' => $branch->code,
                         'name' => $branch->name,
                     ]
                 ]);
    }

    /**
     * Test can update a branch.
     */
    public function test_can_update_branch(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $updateData = [
            'name' => 'Updated Branch Name',
            'city' => 'Irbid',
        ];

        $response = $this->putJson('/api/branches/' . $branch->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Branch Name',
                         'city' => 'Irbid',
                     ]
                 ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Updated Branch Name',
        ]);
    }

    /**
     * Test can delete a branch.
     */
    public function test_can_delete_branch(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson('/api/branches/' . $branch->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Branch deleted successfully',
                 ]);

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    /**
     * Test can get users assigned to a branch.
     */
    public function test_can_get_branch_users(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);
        User::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id
        ]);

        $response = $this->getJson('/api/branches/' . $branch->id . '/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'email']
                         ]
                     ]
                 ]);
        
        $this->assertEquals(3, count($response->json('data.data')));
    }

    /**
     * Test unauthenticated user cannot access API.
     */
    public function test_unauthenticated_user_cannot_access_api(): void
    {
        // Remove authentication
        $this->app->forgetInstance('auth');

        $response = $this->getJson('/api/branches');

        $response->assertStatus(401);
    }
}
