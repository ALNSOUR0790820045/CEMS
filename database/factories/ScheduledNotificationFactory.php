<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+30 days'),
            'repeat_type' => fake()->randomElement(['once', 'daily', 'weekly', 'monthly']),
            'recipients_type' => fake()->randomElement(['user', 'role', 'department', 'all']),
            'recipients_ids' => null,
            'status' => 'pending',
            'created_by_id' => User::factory(),
            'company_id' => Company::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
