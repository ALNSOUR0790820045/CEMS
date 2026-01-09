<?php

namespace Database\Factories;

use App\Models\PunchItemComment;
use App\Models\PunchItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PunchItemComment>
 */
class PunchItemCommentFactory extends Factory
{
    protected $model = PunchItemComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'punch_item_id' => PunchItem::factory(),
            'comment' => fake()->paragraph(),
            'commented_by_id' => User::factory(),
            'comment_type' => fake()->randomElement(['note', 'query', 'response', 'rejection']),
            'attachments' => null,
        ];
    }
}
