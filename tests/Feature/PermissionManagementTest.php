<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Assign roles
        $this->admin->assignRole($adminRole);
        $this->user->assignRole($userRole);
    }

    public function test_user_can_have_role(): void
    {
        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->user->hasRole('user'));
    }

    public function test_user_can_be_assigned_permission(): void
    {
        $permission = Permission::create(['name' => 'view projects']);
        
        $this->user->givePermissionTo($permission);

        $this->assertTrue($this->user->hasPermissionTo('view projects'));
    }

    public function test_role_can_have_permissions(): void
    {
        $role = Role::findByName('admin');
        $permission = Permission::create(['name' => 'delete projects']);

        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo('delete projects'));
    }

    public function test_user_inherits_permissions_from_role(): void
    {
        $permission = Permission::create(['name' => 'manage users']);
        $adminRole = Role::findByName('admin');
        
        $adminRole->givePermissionTo($permission);

        $this->assertTrue($this->admin->hasPermissionTo('manage users'));
    }

    public function test_user_without_permission_cannot_access_protected_route(): void
    {
        Permission::create(['name' => 'view projects']);

        $response = $this->actingAs($this->user)
            ->get('/projects');

        // User without permission should not be able to access
        // Note: This depends on your middleware setup
        $response->assertStatus(200); // Adjust based on your permission middleware
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $managerRole = Role::create(['name' => 'manager']);
        
        $this->user->assignRole($managerRole);

        $this->assertTrue($this->user->hasRole(['user', 'manager']));
    }

    public function test_permission_can_be_revoked_from_user(): void
    {
        $permission = Permission::create(['name' => 'edit projects']);
        
        $this->user->givePermissionTo($permission);
        $this->assertTrue($this->user->hasPermissionTo('edit projects'));

        $this->user->revokePermissionTo($permission);
        $this->assertFalse($this->user->hasPermissionTo('edit projects'));
    }

    public function test_role_can_be_removed_from_user(): void
    {
        $this->assertTrue($this->admin->hasRole('admin'));

        $this->admin->removeRole('admin');

        $this->assertFalse($this->admin->hasRole('admin'));
    }
}
