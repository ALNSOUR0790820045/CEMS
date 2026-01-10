<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcontractor>
 */
class SubcontractorFactory extends Factory
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
            'name_en' => fake()->company(),
            'subcontractor_type' => fake()->randomElement(['specialized', 'general', 'labor_only', 'materials_labor']),
            'trade_category' => fake()->randomElement(['civil', 'electrical', 'mechanical', 'plumbing', 'finishing', 'landscaping', 'other']),
            'commercial_registration' => fake()->numerify('CR-########'),
            'tax_number' => fake()->unique()->numerify('TAX-########'),
            'license_number' => fake()->numerify('LIC-######'),
            'license_expiry' => fake()->dateTimeBetween('now', '+2 years'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'payment_terms' => fake()->randomElement(['cod', '7_days', '15_days', '30_days', '45_days', '60_days']),
            'credit_limit' => fake()->randomFloat(2, 10000, 1000000),
            'retention_percentage' => fake()->randomFloat(2, 0, 10),
            'is_active' => true,
            'is_approved' => fake()->boolean(70),
            'is_blacklisted' => false,
        ];
    }
}
