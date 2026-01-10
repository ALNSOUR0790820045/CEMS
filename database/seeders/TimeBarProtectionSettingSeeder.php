<?php

namespace Database\Seeders;

use App\Models\TimeBarProtectionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeBarProtectionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'company_id' => null, // Global setting
                'entity_type' => 'invoice',
                'protection_days' => 90,
                'protection_type' => 'view_only',
                'is_active' => true,
                'description' => 'Invoices older than 90 days become read-only',
                'excluded_roles' => ['super-admin', 'financial-controller'],
            ],
            [
                'company_id' => null,
                'entity_type' => 'contract',
                'protection_days' => 180,
                'protection_type' => 'full_lock',
                'is_active' => true,
                'description' => 'Contracts older than 180 days are fully locked',
                'excluded_roles' => ['super-admin', 'legal-manager'],
            ],
            [
                'company_id' => null,
                'entity_type' => 'payment',
                'protection_days' => 30,
                'protection_type' => 'approval_required',
                'is_active' => true,
                'description' => 'Payments older than 30 days require approval to edit',
                'excluded_roles' => ['super-admin', 'cfo'],
            ],
            [
                'company_id' => null,
                'entity_type' => 'purchase_order',
                'protection_days' => 60,
                'protection_type' => 'view_only',
                'is_active' => true,
                'description' => 'Purchase orders older than 60 days become read-only',
                'excluded_roles' => ['super-admin', 'procurement-manager'],
            ],
        ];

        foreach ($settings as $setting) {
            TimeBarProtectionSetting::create($setting);
        }
    }
}
