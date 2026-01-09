<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'notification_type' => fake()->randomElement([
                'budget_exceeded',
                'deadline_approaching',
                'approval_pending',
                'low_stock',
                'contract_expiry',
            ]),
            'channel_email' => fake()->boolean(),
            'channel_sms' => fake()->boolean(),
            'channel_push' => fake()->boolean(),
            'channel_in_app' => true,
            'is_enabled' => true,
            'quiet_hours_start' => null,
            'quiet_hours_end' => null,
        ];
    }
}
