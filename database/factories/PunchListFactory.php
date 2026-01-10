<?php

namespace Database\Factories;

use App\Models\PunchList;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PunchList>
 */
class PunchListFactory extends Factory
{
    protected $model = PunchList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = date('Y');
        $sequence = fake()->unique()->numberBetween(1, 9999);

        return [
            'list_number' => 'PL-'.$year.'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT),
            'project_id' => Project::factory(),
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'list_type' => fake()->randomElement(['pre_handover', 'handover', 'defects_liability', 'final']),
            'area_zone' => fake()->optional()->word(),
            'building' => fake()->optional()->word(),
            'floor' => fake()->optional()->word(),
            'discipline' => fake()->optional()->randomElement(['architectural', 'structural', 'mep', 'civil', 'landscape']),
            'contractor_id' => Vendor::factory(),
            'subcontractor_id' => null,
            'inspection_date' => fake()->optional()->date(),
            'inspector_id' => User::factory(),
            'consultant_rep' => fake()->optional()->name(),
            'contractor_rep' => fake()->optional()->name(),
            'total_items' => 0,
            'completed_items' => 0,
            'pending_items' => 0,
            'completion_percentage' => 0,
            'target_completion_date' => fake()->optional()->dateTimeBetween('now', '+60 days'),
            'actual_completion_date' => null,
            'status' => fake()->randomElement(['draft', 'issued', 'in_progress', 'completed', 'verified', 'closed']),
            'issued_by_id' => null,
            'issued_at' => null,
            'verified_by_id' => null,
            'verified_at' => null,
            'closed_by_id' => null,
            'closed_at' => null,
            'notes' => fake()->optional()->paragraph(),
            'company_id' => Company::factory(),
        ];
    }

    /**
     * Indicate that the punch list is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the punch list is issued.
     */
    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'issued',
            'issued_by_id' => User::factory(),
            'issued_at' => now(),
        ]);
    }
}
