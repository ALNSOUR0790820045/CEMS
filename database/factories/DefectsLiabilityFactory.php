<?php

namespace Database\Factories;

use App\Models\DefectsLiability;
use App\Models\Project;
use App\Models\Contract;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DefectsLiabilityFactory extends Factory
{
    protected $model = DefectsLiability::class;

    public function definition(): array
    {
        $takingOverDate = fake()->dateTimeBetween('-6 months', 'now');
        $dlpMonths = 12;
        $dlpStartDate = $takingOverDate;
        $dlpEndDate = (clone $takingOverDate)->modify("+{$dlpMonths} months");

        return [
            'project_id' => Project::factory(),
            'contract_id' => Contract::factory(),
            'retention_id' => null,
            'taking_over_date' => $takingOverDate,
            'dlp_start_date' => $dlpStartDate,
            'dlp_end_date' => $dlpEndDate,
            'dlp_months' => $dlpMonths,
            'final_certificate_date' => null,
            'status' => 'active',
            'extension_months' => 0,
            'extension_reason' => null,
            'defects_reported' => 0,
            'defects_rectified' => 0,
            'performance_bond_released' => false,
            'notes' => fake()->optional()->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
