<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get all permissions
        $allPermissions = Permission::all();

        // Create Super Admin role with all permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($allPermissions);

        // Create Company Admin role with all permissions except certain system settings
        $companyAdmin = Role::create(['name' => 'Company Admin']);
        $companyAdminPermissions = $allPermissions->filter(function ($permission) {
            // Exclude certain system-level permissions
            return !in_array($permission->name, [
                'companies.create',
                'companies.delete',
            ]);
        });
        $companyAdmin->givePermissionTo($companyAdminPermissions);

        // Create Manager role - view, create, edit (no delete)
        $manager = Role::create(['name' => 'Manager']);
        $managerPermissions = $allPermissions->filter(function ($permission) {
            return !str_ends_with($permission->name, '.delete');
        });
        $manager->givePermissionTo($managerPermissions);

        // Create User role - view only
        $user = Role::create(['name' => 'User']);
        $userPermissions = $allPermissions->filter(function ($permission) {
            return str_ends_with($permission->name, '.view');
        });
        $user->givePermissionTo($userPermissions);

        // Create additional specialized roles
        
        // Accountant - Full access to financial modules, view access to others
        $accountant = Role::create(['name' => 'Accountant']);
        $accountantPermissions = $allPermissions->filter(function ($permission) {
            $financialModules = ['accounting', 'invoices', 'contracts', 'guarantees', 'payroll'];
            foreach ($financialModules as $module) {
                if (str_starts_with($permission->name, $module . '.')) {
                    return true;
                }
            }
            return str_ends_with($permission->name, '.view');
        });
        $accountant->givePermissionTo($accountantPermissions);

        // Project Manager - Full access to project modules
        $projectManager = Role::create(['name' => 'Project Manager']);
        $projectManagerPermissions = $allPermissions->filter(function ($permission) {
            $projectModules = ['projects', 'sites', 'equipment', 'maintenance', 'subcontractors', 'consultants'];
            foreach ($projectModules as $module) {
                if (str_starts_with($permission->name, $module . '.')) {
                    return true;
                }
            }
            return str_ends_with($permission->name, '.view');
        });
        $projectManager->givePermissionTo($projectManagerPermissions);

        // HR Manager - Full access to HR modules
        $hrManager = Role::create(['name' => 'HR Manager']);
        $hrManagerPermissions = $allPermissions->filter(function ($permission) {
            $hrModules = ['employees', 'payroll', 'users'];
            foreach ($hrModules as $module) {
                if (str_starts_with($permission->name, $module . '.')) {
                    return true;
                }
            }
            return str_ends_with($permission->name, '.view');
        });
        $hrManager->givePermissionTo($hrManagerPermissions);

        // Warehouse Manager - Full access to warehouse and procurement
        $warehouseManager = Role::create(['name' => 'Warehouse Manager']);
        $warehouseManagerPermissions = $allPermissions->filter(function ($permission) {
            $warehouseModules = ['warehouses', 'procurement'];
            foreach ($warehouseModules as $module) {
                if (str_starts_with($permission->name, $module . '.')) {
                    return true;
                }
            }
            return str_ends_with($permission->name, '.view');
        });
        $warehouseManager->givePermissionTo($warehouseManagerPermissions);
    }
}
