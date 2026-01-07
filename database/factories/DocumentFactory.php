<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_name' => fake()->words(3, true),
            'document_type' => fake()->randomElement(['contract', 'drawing', 'specification', 'certificate', 'report', 'correspondence', 'other']),
            'category' => fake()->randomElement(['Legal', 'Technical', 'Financial', 'HR', 'Other']),
            'version' => '1.0',
            'file_path' => 'documents/' . fake()->uuid() . '.pdf',
            'file_size' => fake()->numberBetween(1000, 5000000),
            'file_type' => 'pdf',
            'description' => fake()->paragraph(),
            'tags' => fake()->words(3),
            'is_confidential' => fake()->boolean(20),
            'status' => fake()->randomElement(['draft', 'review', 'approved', 'archived', 'obsolete']),
        ];
    }
}
