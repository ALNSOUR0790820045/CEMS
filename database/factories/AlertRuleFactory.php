<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventTypes = [
            'budget_exceeded',
            'deadline_approaching',
            'approval_pending',
            'low_stock',
            'contract_expiry',
            'payment_overdue',
        ];

        return [
            'name' => fake()->sentence(3),
            'name_en' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_type' => fake()->randomElement($eventTypes),
            'conditions' => null,
            'recipients_type' => fake()->randomElement(['user', 'role', 'department', 'all']),
            'recipients_ids' => null,
            'channels' => ['email', 'in_app'],
            'message_template' => fake()->paragraph(),
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
