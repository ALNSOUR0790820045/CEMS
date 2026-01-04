<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notification_type' => $this->faker->randomElement([
                'approval_request',
                'document_expiry',
                'invoice_overdue',
                'budget_alert',
                'stock_alert',
                'certification_expiry',
                'task_assignment',
                'system_update'
            ]),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'user_id' => null,
            'role_id' => null,
            'related_entity_type' => null,
            'related_entity_id' => null,
            'is_read' => false,
            'read_at' => null,
            'sent_via_email' => false,
            'sent_via_sms' => false,
            'email_sent_at' => null,
            'sms_sent_at' => null,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
