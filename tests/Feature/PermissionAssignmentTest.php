<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
        
        // Create permissions
        Permission::create(['name' => 'manage-roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'view-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-users', 'guard_name' => 'web']);
    }

    public function test_can_assign_permissions_to_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles/' . $role->id . '/permissions', [
                'permissions' => ['view-users', 'edit-users'],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permissions assigned successfully',
            ]);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('view-users'));
        $this->assertTrue($role->hasPermissionTo('edit-users'));
        $this->assertFalse($role->hasPermissionTo('delete-users'));
    }

    public function test_can_sync_permissions_to_role()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);
        $role->givePermissionTo(['view-users', 'edit-users']);

        // Sync with new permissions (should replace old ones)
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles/' . $role->id . '/permissions', [
                'permissions' => ['delete-users'],
            ]);

        $response->assertStatus(200);

        $role->refresh();
        $this->assertFalse($role->hasPermissionTo('view-users'));
        $this->assertFalse($role->hasPermissionTo('edit-users'));
        $this->assertTrue($role->hasPermissionTo('delete-users'));
    }

    public function test_cannot_assign_invalid_permissions()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-roles');

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles/' . $role->id . '/permissions', [
                'permissions' => ['invalid-permission'],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.0']);
    }

    public function test_unauthorized_user_cannot_assign_permissions()
    {
        $user = User::factory()->create();
        // No permissions given

        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/roles/' . $role->id . '/permissions', [
                'permissions' => ['view-users'],
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to perform this action.',
            ]);
    }
}
