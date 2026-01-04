<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions by module
        $permissions = [
            // User Management
            'users' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
            ],
            
            // Role & Permission Management
            'roles' => [
                'manage-roles',
                'manage-permissions',
                'assign-roles',
            ],
            
            // Company Management
            'companies' => [
                'view-companies',
                'create-companies',
                'edit-companies',
                'delete-companies',
            ],
            
            // Employee Management
            'employees' => [
                'view-employees',
                'create-employees',
                'edit-employees',
                'delete-employees',
            ],
            
            // Department Management
            'departments' => [
                'view-departments',
                'create-departments',
                'edit-departments',
                'delete-departments',
            ],
            
            // Contract Management
            'contracts' => [
                'view-contracts',
                'create-contracts',
                'edit-contracts',
                'delete-contracts',
                'approve-contracts',
            ],
            
            // Document Management
            'documents' => [
                'view-documents',
                'upload-documents',
                'download-documents',
                'delete-documents',
            ],
            
            // Attendance Management
            'attendance' => [
                'view-attendance',
                'record-attendance',
                'edit-attendance',
                'approve-attendance',
            ],
            
            // Leave Management
            'leaves' => [
                'view-leaves',
                'request-leave',
                'approve-leave',
                'reject-leave',
            ],
            
            // Payroll Management
            'payroll' => [
                'view-payroll',
                'process-payroll',
                'approve-payroll',
                'export-payroll',
            ],
            
            // Reports
            'reports' => [
                'view-reports',
                'generate-reports',
                'export-reports',
            ],
            
            // Settings
            'settings' => [
                'view-settings',
                'edit-settings',
            ],
        ];

        // Create all permissions
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create roles and assign permissions

        // 1. Super Admin - All permissions
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Management permissions (no system settings)
        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $adminPermissions = [
            // Users
            'view-users',
            'create-users',
            'edit-users',
            
            // Companies
            'view-companies',
            'create-companies',
            'edit-companies',
            
            // Employees
            'view-employees',
            'create-employees',
            'edit-employees',
            'delete-employees',
            
            // Departments
            'view-departments',
            'create-departments',
            'edit-departments',
            'delete-departments',
            
            // Contracts
            'view-contracts',
            'create-contracts',
            'edit-contracts',
            'approve-contracts',
            
            // Documents
            'view-documents',
            'upload-documents',
            'download-documents',
            'delete-documents',
            
            // Attendance
            'view-attendance',
            'edit-attendance',
            'approve-attendance',
            
            // Leaves
            'view-leaves',
            'approve-leave',
            'reject-leave',
            
            // Payroll
            'view-payroll',
            'process-payroll',
            'approve-payroll',
            'export-payroll',
            
            // Reports
            'view-reports',
            'generate-reports',
            'export-reports',
        ];
        $admin->givePermissionTo($adminPermissions);

        // 3. Manager - Operational permissions
        $manager = Role::create([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);
        $managerPermissions = [
            // Employees
            'view-employees',
            'create-employees',
            'edit-employees',
            
            // Departments
            'view-departments',
            'create-departments',
            'edit-departments',
            
            // Contracts
            'view-contracts',
            'create-contracts',
            'edit-contracts',
            
            // Documents
            'view-documents',
            'upload-documents',
            'download-documents',
            
            // Attendance
            'view-attendance',
            'record-attendance',
            'approve-attendance',
            
            // Leaves
            'view-leaves',
            'approve-leave',
            'reject-leave',
            
            // Payroll
            'view-payroll',
            
            // Reports
            'view-reports',
            'generate-reports',
        ];
        $manager->givePermissionTo($managerPermissions);

        // 4. Employee - Basic permissions
        $employee = Role::create([
            'name' => 'Employee',
            'guard_name' => 'web',
        ]);
        $employeePermissions = [
            // Documents (own)
            'view-documents',
            'download-documents',
            
            // Attendance (own)
            'view-attendance',
            'record-attendance',
            
            // Leaves (own)
            'view-leaves',
            'request-leave',
            
            // Payroll (own)
            'view-payroll',
        ];
        $employee->givePermissionTo($employeePermissions);
    }
}
