<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequisition>
 */
class PurchaseRequisitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requisition_date' => $this->faker->date(),
            'required_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'type' => $this->faker->randomElement(['materials', 'services', 'equipment', 'subcontract']),
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved', 'rejected']),
            'total_estimated_amount' => $this->faker->randomFloat(2, 100, 10000),
            'justification' => $this->faker->sentence(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
