<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DashboardWidget>
 */
class DashboardWidgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dashboard_id' => \App\Models\Dashboard::factory(),
            'widget_type' => fake()->randomElement(['chart', 'kpi', 'table', 'counter', 'gauge']),
            'title' => fake()->words(3, true),
            'data_source' => fake()->word(),
            'config' => null,
            'position_x' => fake()->numberBetween(0, 12),
            'position_y' => fake()->numberBetween(0, 12),
            'width' => fake()->numberBetween(4, 12),
            'height' => fake()->numberBetween(2, 8),
            'refresh_interval' => fake()->randomElement([30, 60, 300, 600]),
            'is_visible' => true,
        ];
    }
}
