<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskRegister>
 */
class RiskRegisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'version' => '1.0',
            'status' => fake()->randomElement(['draft', 'active', 'closed']),
            'prepared_by_id' => User::factory(),
            'review_frequency' => fake()->randomElement(['weekly', 'monthly', 'quarterly']),
            'company_id' => Company::factory(),
        ];
    }
}
