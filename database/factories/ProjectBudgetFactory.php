<?php

namespace Database\Factories;

use App\Models\ProjectBudget;
use App\Models\Project;
use App\Models\Currency;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectBudgetFactory extends Factory
{
    protected $model = ProjectBudget::class;

    public function definition(): array
    {
        $directCosts = $this->faker->numberBetween(500000, 1000000);
        $indirectCosts = $directCosts * 0.2;
        $contingencyPercentage = 10;
        $baseCosts = $directCosts + $indirectCosts;
        $contingencyAmount = ($baseCosts * $contingencyPercentage) / 100;
        $totalBudget = $baseCosts + $contingencyAmount;

        return [
            'budget_number' => 'BUD-' . date('Y') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'project_id' => Project::factory(),
            'contract_id' => null,
            'budget_type' => $this->faker->randomElement(['original', 'revised', 'forecast']),
            'version' => 1,
            'budget_date' => $this->faker->date(),
            'contract_value' => $this->faker->numberBetween(1000000, 5000000),
            'direct_costs' => $directCosts,
            'indirect_costs' => $indirectCosts,
            'contingency_percentage' => $contingencyPercentage,
            'contingency_amount' => $contingencyAmount,
            'total_budget' => $totalBudget,
            'profit_margin_percentage' => 15,
            'expected_profit' => $totalBudget * 0.15,
            'currency_id' => Currency::factory(),
            'status' => 'draft',
            'prepared_by_id' => User::factory(),
            'approved_by_id' => null,
            'approved_at' => null,
            'notes' => $this->faker->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by_id' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'approved_by_id' => User::factory(),
            'approved_at' => now(),
        ]);
    }
}
