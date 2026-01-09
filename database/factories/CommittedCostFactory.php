<?php

namespace Database\Factories;

use App\Models\CommittedCost;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\Vendor;
use App\Models\Currency;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommittedCostFactory extends Factory
{
    protected $model = CommittedCost::class;

    public function definition(): array
    {
        $originalAmount = $this->faker->numberBetween(10000, 500000);

        return [
            'project_id' => Project::factory(),
            'cost_code_id' => CostCode::factory(),
            'budget_item_id' => null,
            'commitment_type' => $this->faker->randomElement(['purchase_order', 'subcontract', 'service_order']),
            'commitment_id' => $this->faker->numberBetween(1, 1000),
            'commitment_number' => 'COM-' . $this->faker->unique()->numberBetween(1000, 9999),
            'vendor_id' => Vendor::factory(),
            'description' => $this->faker->sentence(),
            'original_amount' => $originalAmount,
            'approved_changes' => 0,
            'current_amount' => $originalAmount,
            'invoiced_amount' => 0,
            'remaining_amount' => $originalAmount,
            'currency_id' => Currency::factory(),
            'status' => 'open',
            'company_id' => Company::factory(),
        ];
    }
}
