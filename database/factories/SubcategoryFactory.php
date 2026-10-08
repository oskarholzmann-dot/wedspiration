<?php

// AI-GENERATED (beyond course scope): subcategories — written with Claude Code

namespace Database\Factories;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subcategory>
 */
class SubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Boho', 'Black & white', 'Golden hour', 'Rustic', 'Minimalist',
                'Romantic', 'Vintage', 'Garden', 'City', 'Winter',
            ]),
        ];
    }
}
