<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::firstOrCreate(
            ['slug' => 'test-company'],
            [
                'name' => 'شركة اختبار',
                'name_en' => 'Test Company',
                'email' => 'test@example.com',
                'phone' => '0501234567',
                'country' => 'SA',
                'is_active' => true,
            ]
        );

        // Create a test user
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
    }
}
