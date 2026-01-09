<?php

namespace Database\Factories;

use App\Models\CostCode;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CostCodeFactory extends Factory
{
    protected $model = CostCode::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('####'),
            'name' => $this->faker->words(3, true),
            'name_en' => $this->faker->words(3, true),
            'parent_id' => null,
            'level' => 1,
            'cost_type' => $this->faker->randomElement(['direct', 'indirect']),
            'cost_category' => $this->faker->randomElement(['material', 'labor', 'equipment', 'subcontractor', 'overhead']),
            'is_active' => true,
            'company_id' => Company::factory(),
        ];
    }

    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => CostCode::factory(),
            'level' => 2,
        ]);
    }
}
