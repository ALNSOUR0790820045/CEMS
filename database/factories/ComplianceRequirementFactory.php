<?php

namespace Database\Factories;

use App\Models\ComplianceRequirement;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplianceRequirementFactory extends Factory
{
    protected $model = ComplianceRequirement::class;

    public function definition(): array
    {
        return [
            'code' => 'COMP-' . fake()->unique()->numerify('####'),
            'name' => fake()->words(4, true),
            'name_en' => fake()->words(4, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['safety', 'environmental', 'legal', 'quality', 'financial']),
            'regulation_reference' => fake()->optional()->bothify('REG-####-??'),
            'is_mandatory' => fake()->boolean(70),
            'frequency' => fake()->randomElement(['one_time', 'monthly', 'quarterly', 'annually']),
            'responsible_role' => fake()->randomElement(['Safety Officer', 'Project Manager', 'Compliance Officer']),
            'company_id' => Company::factory(),
        ];
    }
}
