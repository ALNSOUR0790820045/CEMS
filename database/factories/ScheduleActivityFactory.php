<?php

namespace Database\Factories;

use App\Models\ProjectSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleActivityFactory extends Factory
{
    protected $model = \App\Models\ScheduleActivity::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $duration = fake()->numberBetween(1, 30);
        $endDate = (clone $startDate)->modify("+{$duration} days");

        return [
            'project_schedule_id' => ProjectSchedule::factory(),
            'activity_code' => 'ACT-' . fake()->unique()->numerify('####'),
            'wbs_id' => null,
            'name' => fake()->words(3, true),
            'name_en' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'activity_type' => fake()->randomElement(['task', 'milestone', 'summary', 'hammock']),
            'parent_id' => null,
            'level' => 1,
            'planned_start' => $startDate,
            'planned_finish' => $endDate,
            'planned_duration' => $duration,
            'actual_start' => null,
            'actual_finish' => null,
            'actual_duration' => 0,
            'remaining_duration' => $duration,
            'percent_complete' => 0,
            'early_start' => null,
            'early_finish' => null,
            'late_start' => null,
            'late_finish' => null,
            'total_float' => 0,
            'free_float' => 0,
            'is_critical' => false,
            'constraint_type' => 'asap',
            'constraint_date' => null,
            'calendar_id' => null,
            'responsible_id' => null,
            'cost_account_id' => null,
            'budgeted_cost' => fake()->randomFloat(2, 1000, 50000),
            'actual_cost' => 0,
            'earned_value' => 0,
            'status' => fake()->randomElement(['not_started', 'in_progress', 'completed', 'on_hold']),
            'notes' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
