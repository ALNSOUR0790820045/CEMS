<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['info', 'success', 'warning', 'error', 'reminder'];
        $categories = ['system', 'approval', 'deadline', 'alert', 'message'];
        $priorities = ['low', 'normal', 'high', 'urgent'];

        return [
            'type' => fake()->randomElement($types),
            'category' => fake()->randomElement($categories),
            'title' => fake()->sentence(),
            'title_en' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'body_en' => fake()->paragraph(),
            'data' => null,
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'read_at' => null,
            'clicked_at' => null,
            'action_url' => fake()->url(),
            'icon' => 'bell',
            'priority' => fake()->randomElement($priorities),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'company_id' => Company::factory(),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
