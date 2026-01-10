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
            'project_code' => $this->faker->unique()->bothify('PROJ-###'),
            'client_id' => \App\Models\Client::factory(),
            'contract_value' => $this->faker->randomFloat(2, 100000, 10000000),
            'contract_currency_id' => \App\Models\Currency::factory(),
            'contract_start_date' => $this->faker->date(),
            'contract_end_date' => $this->faker->date(),
            'contract_duration_days' => $this->faker->numberBetween(30, 730),
            'project_manager_id' => \App\Models\User::factory(),
            'company_id' => \App\Models\Company::factory(),
            'project_status' => 'execution',
            'is_active' => true,
        ];
    }
}
