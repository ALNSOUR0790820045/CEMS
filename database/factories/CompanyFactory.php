<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'name_en' => fake()->company(),
            'slug' => fake()->slug(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->countryCode(),
            'commercial_registration' => fake()->numerify('##########'),
            'tax_number' => fake()->numerify('###########'),
            'is_active' => true,
        ];
    }
}
