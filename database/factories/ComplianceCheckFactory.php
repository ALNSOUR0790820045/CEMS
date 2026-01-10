<?php

namespace Database\Factories;

use App\Models\ComplianceCheck;
use App\Models\ComplianceRequirement;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplianceCheckFactory extends Factory
{
    protected $model = ComplianceCheck::class;

    public function definition(): array
    {
        return [
            'check_number' => 'CC-' . fake()->year() . '-' . fake()->numerify('####'),
            'compliance_requirement_id' => ComplianceRequirement::factory(),
            'project_id' => null,
            'check_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'status' => 'pending',
            'checked_by_id' => null,
            'findings' => null,
            'corrective_action' => null,
            'evidence_path' => null,
            'next_check_date' => null,
            'company_id' => Company::factory(),
        ];
    }

    public function passed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'passed',
            'checked_by_id' => User::factory(),
            'findings' => fake()->sentence(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'checked_by_id' => User::factory(),
            'findings' => fake()->sentence(),
            'corrective_action' => fake()->sentence(),
        ]);
    }
}
