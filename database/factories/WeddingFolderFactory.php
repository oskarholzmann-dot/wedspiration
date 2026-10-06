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
            'notes' => fake()->optional()->randomElement(self::NOTES),
            'wedding_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
        ];
    }

    /**
     * Example briefing notes a couple might leave for the photographer.
     */
    private const NOTES = [
        'Relaxed, documentary style please - no stiff group poses.',
        'Outdoor ceremony, so we would love lots of natural light shots.',
        'Small wedding with around 40 guests, mostly family.',
        'Please capture the details: rings, flowers and the cake.',
        'We want a golden hour session right after dinner.',
    ];
}
