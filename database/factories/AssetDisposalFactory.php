<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssetDisposal>
 */
class AssetDisposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookValue = fake()->randomFloat(2, 100, 5000);
        $disposalAmount = fake()->randomFloat(2, 0, $bookValue * 1.5);
        
        return [
            'disposal_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'disposal_type' => fake()->randomElement(['sale', 'scrap', 'donation', 'trade_in']),
            'disposal_amount' => $disposalAmount,
            'buyer_name' => fake()->company(),
            'book_value_at_disposal' => $bookValue,
            'gl_journal_entry_id' => null,
            'notes' => fake()->sentence(),
        ];
    }
}
