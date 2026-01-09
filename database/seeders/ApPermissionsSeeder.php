<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ApPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AP Invoice permissions
        $apInvoicePermissions = [
            'ap_invoices.view',
            'ap_invoices.create',
            'ap_invoices.edit',
            'ap_invoices.delete',
            'ap_invoices.approve',
        ];

        foreach ($apInvoicePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // AP Payment permissions
        $apPaymentPermissions = [
            'ap_payments.view',
            'ap_payments.create',
            'ap_payments.edit',
            'ap_payments.delete',
        ];

        foreach ($apPaymentPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // AP Report permissions
        $apReportPermissions = [
            'ap_reports.view',
        ];

        foreach ($apReportPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('AP module permissions created successfully.');
    }
}
