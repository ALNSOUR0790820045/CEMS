<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ReportPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'reports.view_financial',
            'reports.view_ap_ar',
            'reports.view_project',
            'reports.export',
            'reports.schedule',
            'reports.view_all_companies',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Report permissions created successfully!');
    }
}
