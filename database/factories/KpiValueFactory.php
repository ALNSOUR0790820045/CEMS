<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KpiValue>
 */
class KpiValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $targetValue = fake()->randomFloat(2, 1000, 100000);
        $actualValue = fake()->randomFloat(2, 800, 110000);
        $variance = $actualValue - $targetValue;
        $variancePercentage = $targetValue != 0 ? ($variance / $targetValue) * 100 : 0;

        return [
            'kpi_definition_id' => \App\Models\KpiDefinition::factory(),
            'period_date' => fake()->date(),
            'actual_value' => $actualValue,
            'target_value' => $targetValue,
            'variance' => $variance,
            'variance_percentage' => $variancePercentage,
            'status' => fake()->randomElement(['on_track', 'warning', 'critical']),
            'project_id' => null,
            'department_id' => null,
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
