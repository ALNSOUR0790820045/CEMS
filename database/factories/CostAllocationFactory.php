<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CostAllocation>
 */
class CostAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_date' => fake()->date(),
            'source_type' => 'App\Models\Project',
            'source_id' => 1,
            'amount' => fake()->randomFloat(2, 100, 50000),
            'description' => fake()->sentence(),
            'cost_center_id' => \App\Models\CostCenter::factory(),
            'gl_account_id' => \App\Models\GlAccount::factory(),
            'currency_id' => \App\Models\Currency::factory(),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
