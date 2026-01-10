<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentInstrumentsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Currency permissions
        $currencyPermissions = [
            'view_currencies',
            'create_currencies',
            'edit_currencies',
            'delete_currencies',
            'manage_currencies',
            'update_exchange_rates',
        ];

        // Check permissions
        $checkPermissions = [
            'view_checks',
            'create_checks',
            'edit_checks',
            'delete_checks',
            'approve_checks',
            'cancel_checks',
            'print_checks',
            'clear_checks',
        ];

        // Promissory Note permissions
        $promissoryNotePermissions = [
            'view_promissory_notes',
            'create_promissory_notes',
            'edit_promissory_notes',
            'delete_promissory_notes',
            'approve_promissory_notes',
            'print_promissory_notes',
        ];

        // Guarantee permissions (additional to existing)
        $guaranteePermissions = [
            'view_guarantees',
            'create_guarantees',
            'edit_guarantees',
            'delete_guarantees',
            'approve_guarantees',
            'release_guarantees',
            'renew_guarantees',
            'print_guarantees',
        ];

        // Payment Template permissions
        $templatePermissions = [
            'view_payment_templates',
            'create_payment_templates',
            'edit_payment_templates',
            'delete_payment_templates',
            'manage_payment_templates',
        ];

        // Exchange Rate permissions
        $exchangeRatePermissions = [
            'view_exchange_rates',
            'create_exchange_rates',
            'edit_exchange_rates',
            'delete_exchange_rates',
            'update_exchange_rates_bulk',
        ];

        // Report permissions
        $reportPermissions = [
            'view_payment_reports',
            'export_payment_reports',
            'view_cash_flow_forecast',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $currencyPermissions,
            $checkPermissions,
            $promissoryNotePermissions,
            $guaranteePermissions,
            $templatePermissions,
            $exchangeRatePermissions,
            $reportPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo($allPermissions);

        $financeManagerRole = Role::firstOrCreate(['name' => 'Finance Manager']);
        $financeManagerRole->givePermissionTo(array_merge(
            $currencyPermissions,
            $checkPermissions,
            $promissoryNotePermissions,
            $guaranteePermissions,
            $templatePermissions,
            $exchangeRatePermissions,
            $reportPermissions
        ));

        $accountantRole = Role::firstOrCreate(['name' => 'Accountant']);
        $accountantRole->givePermissionTo([
            'view_currencies',
            'view_checks',
            'create_checks',
            'edit_checks',
            'print_checks',
            'view_promissory_notes',
            'create_promissory_notes',
            'edit_promissory_notes',
            'print_promissory_notes',
            'view_guarantees',
            'create_guarantees',
            'edit_guarantees',
            'print_guarantees',
            'view_payment_templates',
            'view_exchange_rates',
            'view_payment_reports',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);
        $viewerRole->givePermissionTo([
            'view_currencies',
            'view_checks',
            'view_promissory_notes',
            'view_guarantees',
            'view_payment_templates',
            'view_exchange_rates',
            'view_payment_reports',
        ]);
    }
}
