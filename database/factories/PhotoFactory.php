<?php

namespace Database\Factories;

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
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
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'category' => fake()->randomElement(PhotoCategory::cases()),
            'image_path' => 'https://picsum.photos/seed/'.fake()->unique()->word().'/800/600',
        ];
    }
}
