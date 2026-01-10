<?php

namespace Database\Factories;

use App\Models\EmployeeLoan;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeLoanFactory extends Factory
{
    protected $model = EmployeeLoan::class;

    public function definition(): array
    {
        $loanAmount = fake()->randomFloat(2, 5000, 50000);
        $totalInstallments = fake()->numberBetween(6, 24);
        $installmentAmount = round($loanAmount / $totalInstallments, 2);

        return [
            'employee_id' => User::factory(),
            'loan_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'loan_amount' => $loanAmount,
            'installment_amount' => $installmentAmount,
            'total_installments' => $totalInstallments,
            'paid_installments' => fake()->numberBetween(0, $totalInstallments - 1),
            'status' => 'active',
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
