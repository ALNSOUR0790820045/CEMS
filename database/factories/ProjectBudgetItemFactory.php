<?php

namespace Database\Factories;

use App\Models\ProjectBudgetItem;
use App\Models\ProjectBudget;
use App\Models\CostCode;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectBudgetItemFactory extends Factory
{
    protected $model = ProjectBudgetItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(100, 1000);
        $unitRate = $this->faker->numberBetween(100, 500);
        $budgetedAmount = $quantity * $unitRate;

        return [
            'project_budget_id' => ProjectBudget::factory(),
            'cost_code_id' => CostCode::factory(),
            'boq_item_id' => null,
            'wbs_id' => null,
            'description' => $this->faker->sentence(),
            'cost_type' => $this->faker->randomElement(['material', 'labor', 'equipment', 'subcontractor', 'overhead', 'other']),
            'quantity' => $quantity,
            'unit_id' => Unit::factory(),
            'unit_rate' => $unitRate,
            'budgeted_amount' => $budgetedAmount,
            'committed_amount' => 0,
            'actual_amount' => 0,
            'variance_amount' => 0,
            'variance_percentage' => 0,
            'forecast_amount' => $budgetedAmount,
            'estimate_to_complete' => $budgetedAmount,
            'estimate_at_completion' => $budgetedAmount,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
