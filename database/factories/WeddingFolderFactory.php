<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeddingFolder>
 */
class WeddingFolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->firstName().' & '.fake()->firstName(),
            'notes' => fake()->optional()->paragraph(),
            'wedding_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
        ];
    }
}
