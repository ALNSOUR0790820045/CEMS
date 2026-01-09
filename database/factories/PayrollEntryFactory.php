<?php

namespace Database\Factories;

use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollEntryFactory extends Factory
{
    protected $model = PayrollEntry::class;

    public function definition(): array
    {
        return [
            'payroll_period_id' => PayrollPeriod::factory(),
            'employee_id' => User::factory(),
            'basic_salary' => fake()->randomFloat(2, 3000, 10000),
            'total_allowances' => fake()->randomFloat(2, 0, 2000),
            'total_deductions' => fake()->randomFloat(2, 0, 1000),
            'days_worked' => fake()->numberBetween(20, 30),
            'days_absent' => fake()->numberBetween(0, 5),
            'overtime_hours' => fake()->randomFloat(2, 0, 20),
            'overtime_amount' => fake()->randomFloat(2, 0, 500),
            'status' => 'draft',
            'payment_method' => fake()->randomElement(['bank_transfer', 'cash', 'check']),
            'company_id' => Company::factory(),
        ];
    }
}
