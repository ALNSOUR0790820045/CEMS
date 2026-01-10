<?php

namespace Database\Factories;

use App\Models\VarianceAnalysis;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VarianceAnalysisFactory extends Factory
{
    protected $model = VarianceAnalysis::class;

    public function definition(): array
    {
        $budgetedAmount = $this->faker->numberBetween(50000, 200000);
        $actualAmount = $this->faker->numberBetween(40000, 220000);
        $varianceAmount = $budgetedAmount - $actualAmount;
        $variancePercentage = $budgetedAmount > 0 ? ($varianceAmount / $budgetedAmount) * 100 : 0;

        return [
            'project_id' => Project::factory(),
            'analysis_date' => $this->faker->date(),
            'period_month' => $this->faker->numberBetween(1, 12),
            'period_year' => $this->faker->numberBetween(2023, 2026),
            'cost_code_id' => CostCode::factory(),
            'budgeted_amount' => $budgetedAmount,
            'actual_amount' => $actualAmount,
            'variance_amount' => $varianceAmount,
            'variance_percentage' => $variancePercentage,
            'variance_type' => $varianceAmount >= 0 ? 'favorable' : 'unfavorable',
            'variance_reason' => $this->faker->optional()->sentence(),
            'corrective_action' => $this->faker->optional()->sentence(),
            'responsible_person_id' => User::factory(),
            'status' => $this->faker->randomElement(['identified', 'analyzed', 'action_taken', 'closed']),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
