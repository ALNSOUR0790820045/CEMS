<?php

namespace Database\Factories;

use App\Models\AdvancePayment;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvancePaymentFactory extends Factory
{
    protected $model = AdvancePayment::class;

    public function definition(): array
    {
        $advanceAmount = fake()->randomFloat(2, 10000, 100000);
        
        return [
            'project_id' => Project::factory(),
            'contract_id' => Contract::factory(),
            'advance_type' => fake()->randomElement(['mobilization', 'materials', 'equipment']),
            'advance_percentage' => fake()->randomFloat(2, 10, 30),
            'advance_amount' => $advanceAmount,
            'currency_id' => Currency::factory(),
            'payment_date' => null,
            'guarantee_required' => true,
            'guarantee_id' => null,
            'recovery_start_percentage' => fake()->randomFloat(2, 0, 20),
            'recovery_percentage' => fake()->randomFloat(2, 5, 15),
            'recovered_amount' => 0,
            'balance_amount' => $advanceAmount,
            'status' => 'pending',
            'approved_by_id' => null,
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
