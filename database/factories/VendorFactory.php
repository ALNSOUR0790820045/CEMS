<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'contact_person' => fake()->name(),
            'tax_number' => fake()->numerify('TAX######'),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
