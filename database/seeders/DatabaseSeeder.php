<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed basic data first
        $this->call([
            // Core system data
            RolesAndPermissionsSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            
            // Master data
            CurrencySeeder::class,
            CountrySeeder::class,
            CitySeeder::class,
            BranchSeeder::class,
        ]);
        
        // User::factory(10)->create();
        
        // Optional: Create test user if UserSeeder doesn't exist
        if (!class_exists(UserSeeder::class)) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}