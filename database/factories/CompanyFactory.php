<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company:: class;

    public function definition(): array
    {
        $name = fake()->company();
        
        return [
            'name' => $name,
            'name_en' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => 'JO',
            'commercial_registration' => fake()->numerify('CR-########'),
            'tax_number' => fake()->numerify('TAX-########'),
            'logo' => null,
            'is_active' => true,
            'established_date' => fake()->date(),
            'license_number' => fake()->numerify('LIC-########'),
            'license_expiry' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
        ];
    }
}