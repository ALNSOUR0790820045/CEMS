<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AlertRule>
 */
class AlertRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rule_name' => $this->faker->words(3, true),
            'rule_type' => $this->faker->randomElement([
                'approval_pending',
                'document_expiring',
                'invoice_overdue',
                'budget_exceeded',
                'stock_low',
                'certification_expiring'
            ]),
            'trigger_condition' => [
                'threshold' => $this->faker->numberBetween(100, 10000),
                'days_before' => $this->faker->numberBetween(1, 30),
            ],
            'notification_template' => $this->faker->sentence(),
            'target_users' => [$this->faker->numberBetween(1, 10)],
            'target_roles' => [$this->faker->numberBetween(1, 5)],
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
