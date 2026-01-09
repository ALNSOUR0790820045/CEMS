<?php

namespace Database\Factories;

use App\Models\PunchItem;
use App\Models\PunchList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PunchItem>
 */
class PunchItemFactory extends Factory
{
    protected $model = PunchItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_number' => 'PL-'.fake()->year().'-'.fake()->unique()->numberBetween(1000, 9999).'-'.str_pad(fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'punch_list_id' => PunchList::factory(),
            'location' => fake()->optional()->words(2, true),
            'room_number' => fake()->optional()->numerify('Room ###'),
            'grid_reference' => fake()->optional()->bothify('?##-?##'),
            'element' => fake()->optional()->word(),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['defect', 'incomplete', 'damage', 'missing', 'wrong']),
            'severity' => fake()->randomElement(['minor', 'major', 'critical']),
            'discipline' => fake()->randomElement(['architectural', 'structural', 'electrical', 'mechanical', 'plumbing', 'fire', 'hvac']),
            'trade' => fake()->optional()->word(),
            'responsible_party' => fake()->optional()->company(),
            'assigned_to_id' => null,
            'photos' => null,
            'completion_photos' => null,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'completed_date' => null,
            'verified_date' => null,
            'status' => fake()->randomElement(['open', 'in_progress', 'completed', 'verified', 'rejected']),
            'rejection_reason' => null,
            'completion_remarks' => null,
            'verified_by_id' => null,
            'cost_to_rectify' => fake()->optional()->randomFloat(2, 100, 10000),
            'back_charge' => fake()->boolean(20),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
        ];
    }

    /**
     * Indicate that the item is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * Indicate that the item is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_date' => now(),
        ]);
    }

    /**
     * Indicate that the item is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verified',
            'completed_date' => now()->subDays(1),
            'verified_date' => now(),
            'verified_by_id' => User::factory(),
        ]);
    }
}
