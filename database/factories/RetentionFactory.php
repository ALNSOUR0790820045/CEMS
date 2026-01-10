<?php

namespace Database\Factories;

use App\Models\Retention;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class RetentionFactory extends Factory
{
    protected $model = Retention::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'contract_id' => Contract::factory(),
            'retention_type' => fake()->randomElement(['performance', 'defects_liability', 'advance_payment', 'materials']),
            'retention_percentage' => fake()->randomFloat(2, 5, 10),
            'max_retention_percentage' => fake()->randomFloat(2, 10, 15),
            'release_schedule' => fake()->randomElement(['single', 'staged']),
            'first_release_percentage' => 50,
            'first_release_condition' => 'practical_completion',
            'second_release_percentage' => 50,
            'second_release_condition' => 'defects_liability_end',
            'defects_liability_period_months' => 12,
            'dlp_start_date' => null,
            'dlp_end_date' => null,
            'total_contract_value' => fake()->randomFloat(2, 100000, 1000000),
            'total_retention_amount' => 0,
            'released_amount' => 0,
            'balance_amount' => 0,
            'currency_id' => Currency::factory(),
            'status' => 'accumulating',
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
