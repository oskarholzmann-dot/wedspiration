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
        $category = fake()->randomElement(PhotoCategory::cases());

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(self::TITLES[$category->value]),
            'description' => fake()->optional()->randomElement(self::DESCRIPTIONS),
            'category' => $category,
            'image_path' => 'https://picsum.photos/seed/'.fake()->unique()->uuid().'/800/600',
        ];
    }

    /**
     * Example titles per category, so seeded photos look like real inspiration.
     */
    private const TITLES = [
        'ceremony' => ['Vows under the old oak', 'First look by the lake', 'Ring exchange at sunset', 'Walking down the aisle'],
        'reception' => ['Long table dinner', 'First dance', 'Champagne tower', 'Fairy lights in the barn'],
        'dress' => ['Lace sleeves', 'Minimalist silk gown', 'Long cathedral veil', 'Boho dress with open back'],
        'flowers' => ['Peony bridal bouquet', 'Wildflower arch', 'Eucalyptus table runner', 'Pampas grass decor'],
        'venue' => ['Vineyard in Tuscany', 'Rustic barn', 'Beach at golden hour', 'Castle garden'],
        'details' => ['Hand-lettered place cards', 'Vintage ring box', 'Wax-sealed invitations', 'Naked cake with berries'],
    ];

    /**
     * Example descriptions a couple might add.
     */
    private const DESCRIPTIONS = [
        'We love the soft, natural light in this one.',
        'Exactly the mood we want for our day.',
        'Could we get a similar shot with our families?',
        'Love the colours - very close to our palette.',
        'Saw this on a friend\'s wedding and fell in love.',
    ];
}
