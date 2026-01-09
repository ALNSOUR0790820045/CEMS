<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectScheduleFactory extends Factory
{
    protected $model = \App\Models\ProjectSchedule::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+1 year');

        return [
            'schedule_number' => 'SCH-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'project_id' => Project::factory(),
            'name' => fake()->words(3, true) . ' Schedule',
            'description' => fake()->sentence(),
            'schedule_type' => fake()->randomElement(['baseline', 'current', 'revised', 'what_if']),
            'version' => fake()->numberBetween(1, 5),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $startDate->diff($endDate)->days,
            'working_days_per_week' => 5,
            'hours_per_day' => 8.0,
            'calendar_id' => null,
            'data_date' => fake()->dateTimeBetween($startDate, $endDate),
            'status' => fake()->randomElement(['draft', 'baseline', 'approved', 'superseded']),
            'prepared_by_id' => User::factory(),
            'approved_by_id' => null,
            'approved_at' => null,
            'company_id' => Company::factory(),
        ];
    }
}
