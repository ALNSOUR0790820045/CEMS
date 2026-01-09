<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('DEPT-###'),
            'name' => fake()->words(2, true),
            'name_en' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }
}
