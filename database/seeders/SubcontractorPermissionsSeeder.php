<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SubcontractorPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define subcontractor permissions
        $permissions = [
            // Subcontractors
            'subcontractors.view',
            'subcontractors.create',
            'subcontractors.edit',
            'subcontractors.delete',
            'subcontractors.approve',
            'subcontractors.blacklist',
            
            // Agreements
            'subcontractors.manage_agreements',
            'subcontractors.view_agreements',
            'subcontractors.create_agreements',
            'subcontractors.edit_agreements',
            'subcontractors.delete_agreements',
            
            // Work Orders
            'subcontractors.manage_work_orders',
            'subcontractors.view_work_orders',
            'subcontractors.create_work_orders',
            'subcontractors.edit_work_orders',
            'subcontractors.delete_work_orders',
            
            // IPCs
            'subcontractors.manage_ipcs',
            'subcontractors.view_ipcs',
            'subcontractors.create_ipcs',
            'subcontractors.edit_ipcs',
            'subcontractors.delete_ipcs',
            'subcontractors.approve_ipcs',
            'subcontractors.review_ipcs',
            
            // Evaluations
            'subcontractors.evaluate',
            'subcontractors.view_evaluations',
            'subcontractors.create_evaluations',
            'subcontractors.edit_evaluations',
            'subcontractors.delete_evaluations',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles (if roles exist)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'subcontractors.view',
                'subcontractors.create',
                'subcontractors.edit',
                'subcontractors.approve',
                'subcontractors.manage_agreements',
                'subcontractors.manage_work_orders',
                'subcontractors.manage_ipcs',
                'subcontractors.approve_ipcs',
                'subcontractors.evaluate',
            ]);
        }

        $this->command->info('Subcontractor permissions created successfully!');
    }
}

