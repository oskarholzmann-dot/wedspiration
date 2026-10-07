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
     * Position in the list of sample images, so seeded photos repeat as little as possible.
     */
    private static int $nextImage = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image = self::IMAGES[self::$nextImage++ % count(self::IMAGES)];

        return [
            'user_id' => User::factory(),
            'title' => $image['title'],
            'description' => fake()->optional()->randomElement(self::DESCRIPTIONS),
            'category' => PhotoCategory::from($image['category']),
            'image_path' => 'images/seed/'.$image['file'],
        ];
    }

    /**
     * Sample wedding photos in public/images/seed (free Unsplash photos, see CREDITS.md there).
     */
    private const IMAGES = [
        ['file' => 'ceremony-1.jpg', 'category' => 'ceremony', 'title' => 'Rose-lined garden aisle'],
        ['file' => 'reception-1.jpg', 'category' => 'reception', 'title' => 'Long table with wildflowers'],
        ['file' => 'dress-1.jpg', 'category' => 'dress', 'title' => 'Lace back detail'],
        ['file' => 'flowers-1.jpg', 'category' => 'flowers', 'title' => 'Peach rose bouquet'],
        ['file' => 'venue-1.jpg', 'category' => 'venue', 'title' => 'Garden gazebo'],
        ['file' => 'details-1.jpg', 'category' => 'details', 'title' => 'Rings on the vows'],
        ['file' => 'ceremony-2.jpg', 'category' => 'ceremony', 'title' => 'Lakeside ceremony'],
        ['file' => 'reception-2.jpg', 'category' => 'reception', 'title' => 'Dancing the night away'],
        ['file' => 'dress-2.jpg', 'category' => 'dress', 'title' => 'Ball gown by the window'],
        ['file' => 'flowers-2.jpg', 'category' => 'flowers', 'title' => 'Eucalyptus and thistle bouquet'],
        ['file' => 'venue-2.jpg', 'category' => 'venue', 'title' => 'Fairy-light aisle at night'],
        ['file' => 'details-2.jpg', 'category' => 'details', 'title' => 'Bridal heels and bouquet'],
        ['file' => 'ceremony-3.jpg', 'category' => 'ceremony', 'title' => 'Ring exchange in black and white'],
        ['file' => 'reception-3.jpg', 'category' => 'reception', 'title' => 'Ballroom reception tables'],
        ['file' => 'dress-3.jpg', 'category' => 'dress', 'title' => 'Flowing veil on the stairs'],
        ['file' => 'flowers-3.jpg', 'category' => 'flowers', 'title' => 'White and blush bouquet'],
        ['file' => 'venue-3.jpg', 'category' => 'venue', 'title' => 'Light-filled church'],
        ['file' => 'details-3.jpg', 'category' => 'details', 'title' => 'Ring resting on roses'],
        ['file' => 'ceremony-4.jpg', 'category' => 'ceremony', 'title' => 'Autumn forest ceremony'],
        ['file' => 'reception-4.jpg', 'category' => 'reception', 'title' => 'Candlelit dinner table'],
        ['file' => 'dress-4.jpg', 'category' => 'dress', 'title' => 'Boho dress at golden hour'],
        ['file' => 'flowers-4.jpg', 'category' => 'flowers', 'title' => 'Wild autumn bouquet'],
        ['file' => 'venue-4.jpg', 'category' => 'venue', 'title' => 'Rustic barn with drapes'],
        ['file' => 'details-4.jpg', 'category' => 'details', 'title' => 'Gold wedding bands'],
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
