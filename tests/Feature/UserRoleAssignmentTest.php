<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
        
        // Create permissions
        Permission::create(['name' => 'assign-roles', 'guard_name' => 'web']);
    }

    public function test_can_assign_role_to_user()
    {
        $adminUser = User::factory()->create();
        $adminUser->givePermissionTo('assign-roles');

        $targetUser = User::factory()->create();
        $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $response = $this->actingAs($adminUser, 'sanctum')
            ->postJson('/api/users/' . $targetUser->id . '/assign-role', [
                'role' => 'Manager',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Role assigned successfully',
            ]);

        $targetUser->refresh();
        $this->assertTrue($targetUser->hasRole('Manager'));
    }

    public function test_cannot_assign_invalid_role()
    {
        $adminUser = User::factory()->create();
        $adminUser->givePermissionTo('assign-roles');

        $targetUser = User::factory()->create();

        $response = $this->actingAs($adminUser, 'sanctum')
            ->postJson('/api/users/' . $targetUser->id . '/assign-role', [
                'role' => 'NonExistentRole',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_unauthorized_user_cannot_assign_roles()
    {
        $user = User::factory()->create();
        // No permissions given

        $targetUser = User::factory()->create();
        $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users/' . $targetUser->id . '/assign-role', [
                'role' => 'Manager',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_returns_404_for_nonexistent_user()
    {
        $adminUser = User::factory()->create();
        $adminUser->givePermissionTo('assign-roles');

        $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $response = $this->actingAs($adminUser, 'sanctum')
            ->postJson('/api/users/99999/assign-role', [
                'role' => 'Manager',
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'User not found',
            ]);
    }
}
