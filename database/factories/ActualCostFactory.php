<?php

namespace Database\Factories;

use App\Models\ActualCost;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\Vendor;
use App\Models\Currency;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActualCostFactory extends Factory
{
    protected $model = ActualCost::class;

    public function definition(): array
    {
        $amount = $this->faker->numberBetween(1000, 100000);

        return [
            'project_id' => Project::factory(),
            'cost_code_id' => CostCode::factory(),
            'budget_item_id' => null,
            'transaction_date' => $this->faker->date(),
            'reference_type' => $this->faker->randomElement(['invoice', 'payroll', 'petty_cash', 'journal']),
            'reference_id' => $this->faker->numberBetween(1, 1000),
            'reference_number' => 'REF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'vendor_id' => Vendor::factory(),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'unit_id' => null,
            'unit_rate' => $this->faker->numberBetween(100, 1000),
            'amount' => $amount,
            'currency_id' => Currency::factory(),
            'exchange_rate' => 1,
            'amount_local' => $amount,
            'posted_by_id' => User::factory(),
            'posted_at' => now(),
            'company_id' => Company::factory(),
        ];
    }
}
