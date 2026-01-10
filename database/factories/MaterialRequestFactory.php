<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialRequest>
 */
class MaterialRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_number' => \App\Models\MaterialRequest::generateRequestNumber(),
            'request_date' => $this->faker->date(),
            'project_id' => \App\Models\Project::factory(),
            'department_id' => \App\Models\Department::factory(),
            'requested_by_id' => \App\Models\User::factory(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'required_date' => $this->faker->optional()->date(),
            'status' => $this->faker->randomElement(['draft', 'pending_approval', 'approved']),
            'notes' => $this->faker->optional()->sentence(),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
