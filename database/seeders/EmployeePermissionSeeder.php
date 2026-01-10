<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EmployeePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'employees.manage_documents',
            'employees.manage_dependents',
            'employees.manage_qualifications',
            'employees.manage_skills',
            'employees.view_salary',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
