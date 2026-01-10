<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'code' => strtoupper(fake()->unique()->bothify('BR-###??')),
            'name' => fake()->company() . ' Branch',
            'name_en' => fake()->company() . ' Branch',
            'region' => fake()->state(),
            'city' => fake()->city(),
            'country' => 'JO',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'manager_id' => null,
            'is_active' => fake()->boolean(80),
            'is_headquarters' => false,
        ];
    }
}
