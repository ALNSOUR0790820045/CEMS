<?php

namespace Database\Factories;

use App\Models\CertificationRenewal;
use App\Models\Certification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationRenewalFactory extends Factory
{
    protected $model = CertificationRenewal::class;

    public function definition(): array
    {
        $oldDate = fake()->dateTimeBetween('-1 year', '-1 month');
        $newDate = fake()->dateTimeBetween($oldDate, '+2 years');
        
        return [
            'renewal_number' => 'RN-' . fake()->year() . '-' . fake()->numerify('####'),
            'certification_id' => Certification::factory(),
            'old_expiry_date' => $oldDate,
            'new_expiry_date' => $newDate,
            'renewal_cost' => fake()->randomFloat(2, 100, 5000),
            'renewal_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'processed_by_id' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
