<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Project;
use App\Models\RiskRegister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Risk>
 */
class RiskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['technical', 'financial', 'schedule', 'safety', 'environmental', 'contractual', 'resource', 'external'];
        $probabilities = ['very_low', 'low', 'medium', 'high', 'very_high'];
        $impacts = ['very_low', 'low', 'medium', 'high', 'very_high'];
        $strategies = ['avoid', 'mitigate', 'transfer', 'accept'];

        $probabilityScore = fake()->numberBetween(1, 5);
        $impactScore = fake()->numberBetween(1, 5);

        return [
            'risk_register_id' => RiskRegister::factory(),
            'project_id' => Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'source' => fake()->sentence(),
            'identification_date' => fake()->date(),
            'identified_by_id' => User::factory(),
            'probability' => fake()->randomElement($probabilities),
            'probability_score' => $probabilityScore,
            'impact' => fake()->randomElement($impacts),
            'impact_score' => $impactScore,
            'cost_impact_min' => fake()->numberBetween(10000, 50000),
            'cost_impact_max' => fake()->numberBetween(50000, 200000),
            'cost_impact_expected' => fake()->numberBetween(30000, 100000),
            'schedule_impact_days' => fake()->numberBetween(5, 30),
            'response_strategy' => fake()->randomElement($strategies),
            'response_plan' => fake()->paragraph(),
            'status' => fake()->randomElement(['identified', 'analyzing', 'responding', 'monitoring', 'closed']),
            'company_id' => Company::factory(),
        ];
    }
}
