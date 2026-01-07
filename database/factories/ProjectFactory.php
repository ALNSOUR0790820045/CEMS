<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => $this->faker->unique()->bothify('PROJ-###'),
            'project_code' => $this->faker->unique()->bothify('PROJ-###'),
            'company_id' => \App\Models\Company::factory(),
            'department_id' => \App\Models\Department::factory(),
            'status' => 'active',
            'is_active' => true,
        ];
    }
}
