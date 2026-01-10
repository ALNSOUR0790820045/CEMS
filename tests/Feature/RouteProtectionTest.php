<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
        
        // Create necessary permissions
        Permission::create(['name' => 'manage-roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage-permissions', 'guard_name' => 'web']);
        Permission::create(['name' => 'assign-roles', 'guard_name' => 'web']);
    }

    public function test_unauthenticated_user_cannot_access_roles_api()
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(401);
    }

    public function test_user_without_permission_cannot_access_roles_api()
    {
        $user = User::factory()->create();
        // User has no permissions

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/roles');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_user_with_permission_can_access_roles_api()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_user_without_permission_cannot_access_permissions_api()
    {
        $user = User::factory()->create();
        // User has no permissions

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_access_permissions_api()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-permissions');

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_role_based_access_control()
    {
        // Create a role with specific permissions
        $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo('manage-roles');

        $user = User::factory()->create();
        $user->assignRole('Manager');

        // User with Manager role should be able to access roles API
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/roles');

        $response->assertStatus(200);
    }

    public function test_permission_middleware_protects_create_role_endpoint()
    {
        $userWithoutPermission = User::factory()->create();
        
        $response = $this->actingAs($userWithoutPermission, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'Test Role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(403);

        // Now test with permission
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo('manage-roles');

        $response = $this->actingAs($userWithPermission, 'sanctum')
            ->postJson('/api/roles', [
                'name' => 'Test Role',
                'guard_name' => 'web',
            ]);

        $response->assertStatus(201);
    }

    public function test_permission_middleware_protects_user_role_assignment()
    {
        $userWithoutPermission = User::factory()->create();
        $targetUser = User::factory()->create();
        $role = Role::create(['name' => 'Employee', 'guard_name' => 'web']);

        $response = $this->actingAs($userWithoutPermission, 'sanctum')
            ->postJson('/api/users/' . $targetUser->id . '/assign-role', [
                'role' => 'Employee',
            ]);

        $response->assertStatus(403);

        // Now test with permission
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo('assign-roles');

        $response = $this->actingAs($userWithPermission, 'sanctum')
            ->postJson('/api/users/' . $targetUser->id . '/assign-role', [
                'role' => 'Employee',
            ]);

        $response->assertStatus(200);
    }
}
