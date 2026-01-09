<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // Companies
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',

            // Master Data - Countries
            'countries.view',
            'countries.create',
            'countries.edit',
            'countries.delete',

            // Master Data - Cities
            'cities.view',
            'cities.create',
            'cities.edit',
            'cities.delete',

            // Master Data - Currencies
            'currencies.view',
            'currencies.create',
            'currencies.edit',
            'currencies.delete',

            // Master Data - Units
            'units.view',
            'units.create',
            'units.edit',
            'units.delete',

            // Master Data - Payment Terms
            'payment-terms.view',
            'payment-terms.create',
            'payment-terms.edit',
            'payment-terms.delete',

            // Users & Roles
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Organization - Branches
            'branches.view',
            'branches.create',
            'branches.edit',
            'branches.delete',

            // Organization - Departments
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',

            // Projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',

            // Project Sites
            'sites.view',
            'sites.create',
            'sites.edit',
            'sites.delete',

            // Accounting
            'accounting.view',
            'accounting.create',
            'accounting.edit',
            'accounting.delete',

            // Invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',

            // Contracts
            'contracts.view',
            'contracts.create',
            'contracts.edit',
            'contracts.delete',

            // Guarantees
            'guarantees.view',
            'guarantees.create',
            'guarantees.edit',
            'guarantees.delete',

            // Procurement
            'procurement.view',
            'procurement.create',
            'procurement.edit',
            'procurement.delete',

            // Warehouses
            'warehouses.view',
            'warehouses.create',
            'warehouses.edit',
            'warehouses.delete',

            // Employees
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // Payroll
            'payroll.view',
            'payroll.create',
            'payroll.edit',
            'payroll.delete',

            // Subcontractors
            'subcontractors.view',
            'subcontractors.create',
            'subcontractors.edit',
            'subcontractors.delete',

            // Consultants
            'consultants.view',
            'consultants.create',
            'consultants.edit',
            'consultants.delete',

            // Tenders
            'tenders.view',
            'tenders.create',
            'tenders.edit',
            'tenders.delete',

            // Quotes
            'quotes.view',
            'quotes.create',
            'quotes.edit',
            'quotes.delete',

            // Archive
            'archive.view',
            'archive.create',
            'archive.edit',
            'archive.delete',

            // Correspondence
            'correspondence.view',
            'correspondence.create',
            'correspondence.edit',
            'correspondence.delete',

            // Equipment
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',

            // Maintenance
            'maintenance.view',
            'maintenance.create',
            'maintenance.edit',
            'maintenance.delete',

            // Reports
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',

            // Settings
            'settings.view',
            'settings.edit',

            // Backups
            'backups.view',
            'backups.create',
            'backups.restore',
            'backups.delete',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
