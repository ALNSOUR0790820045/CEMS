<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollPeriodFactory extends Factory
{
    protected $model = PayrollPeriod::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', 'now');
        $endDate = (clone $startDate)->modify('+30 days');
        $paymentDate = (clone $endDate)->modify('+1 day');

        return [
            'period_name' => fake()->monthName() . ' ' . fake()->year(),
            'period_type' => 'monthly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_date' => $paymentDate,
            'status' => 'open',
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'company_id' => Company::factory(),
        ];
    }
}
