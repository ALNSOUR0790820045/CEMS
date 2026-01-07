<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations and seeders
        $this->artisan('migrate');
        
        // Create permissions needed for tests
        Permission::create(['name' => 'manage-roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'view-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-users', 'guard_name' => 'web']);
    }

    public function test_can_create_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'Test Role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Role created successfully',
            ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
        ]);
    }

    public function test_can_list_roles()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        Role::create(['name' => 'Test Role 1', 'guard_name' => 'web']);
        Role::create(['name' => 'Test Role 2', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_single_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/roles/' . $role->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test Role',
                ],
            ]);
    }

    public function test_can_update_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $role = Role::create(['name' => 'Old Role Name', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/roles/' . $role->id, [
                'name' => 'New Role Name',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Role updated successfully',
            ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'New Role Name',
        ]);
    }

    public function test_unauthorized_user_cannot_create_role()
    {
        $user = User::factory()->create();
        // No permissions given

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'Test Role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_cannot_create_duplicate_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        Role::create(['name' => 'Existing Role', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'Existing Role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
