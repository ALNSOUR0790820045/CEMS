<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@cems.local',
            'password' => Hash::make('password'),
            'job_title' => 'مدير النظام',
            'employee_id' => 'EMP001',
            'is_active' => true,
        ]);
    }
}
