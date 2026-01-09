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

        // Seed countries, cities, and currencies
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            TenderSeeder::class,
        ]);
    }
}