<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KpiDefinition>
 */
class KpiDefinitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'name' => fake()->words(3, true),
            'name_en' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['financial', 'operational', 'hr', 'project']),
            'calculation_formula' => fake()->sentence(),
            'unit' => fake()->randomElement(['percentage', 'currency', 'number', 'days']),
            'target_value' => fake()->randomFloat(2, 1000, 100000),
            'warning_threshold' => fake()->randomFloat(2, 100, 1000),
            'critical_threshold' => fake()->randomFloat(2, 200, 2000),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'quarterly']),
            'is_active' => true,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
