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
        // Seed countries first
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
        ]);
        
        // User::factory(10)->create();

        // Seed countries, cities, and currencies
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            TenderSeeder::class,
        ]);
    }
}