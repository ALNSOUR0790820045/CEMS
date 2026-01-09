<?php

namespace Database\Factories;

use App\Models\Certification;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    protected $model = Certification::class;

    public function definition(): array
    {
        return [
            'certification_number' => 'CERT-' . fake()->year() . '-' . fake()->numerify('####'),
            'name' => fake()->words(3, true),
            'name_en' => fake()->words(3, true),
            'type' => fake()->randomElement(['license', 'permit', 'certificate', 'registration', 'insurance']),
            'category' => fake()->randomElement(['company', 'project', 'employee', 'equipment', 'safety']),
            'issuing_authority' => fake()->company(),
            'issue_date' => fake()->dateTimeBetween('-2 years', '-1 year'),
            'expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
            'renewal_date' => null,
            'status' => 'active',
            'reference_type' => null,
            'reference_id' => null,
            'cost' => fake()->randomFloat(2, 100, 10000),
            'currency_id' => Currency::factory(),
            'attachment_path' => null,
            'reminder_days' => fake()->randomElement([30, 60, 90]),
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }

    public function expiring(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => fake()->dateTimeBetween('now', '+30 days'),
            'status' => 'active',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'status' => 'expired',
        ]);
    }
}
