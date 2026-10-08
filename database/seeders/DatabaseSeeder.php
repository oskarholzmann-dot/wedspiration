<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The admin account the teacher logs in with
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);

        // A known regular user, handy to test what a non-admin can do
        $couple = User::factory()->create([
            'name' => 'Anna & Ben',
            'email' => 'couple@example.com',
        ]);

        $otherUsers = User::factory()->count(5)->create();

        foreach ([$admin, $couple, ...$otherUsers] as $user) {
            $this->createFolderWithPhotos($user);
        }

        // AI-GENERATED (beyond course scope): subcategories — written with Claude Code
        // A few subcategories, each with some of the photos sorted into it
        $subcategories = Subcategory::factory()->count(4)->create();

        Photo::inRandomOrder()->take(12)->get()->each(
            fn (Photo $photo) => $photo->subcategory()->associate($subcategories->random())->save()
        );
    }

    /**
     * Give the user a wedding folder with 3 to 4 photos, the same way an upload does.
     */
    private function createFolderWithPhotos(User $user): void
    {
        $folder = WeddingFolder::factory()->for($user)->create();

        $photos = Photo::factory()
            ->count(fake()->numberBetween(3, 4))
            ->for($user)
            ->create();

        $folder->photos()->attach($photos);
    }
}
