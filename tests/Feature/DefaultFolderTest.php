<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a new user gets a wedding folder when registering', function () {
    $this->post('/register', [
        'name' => 'Anna',
        'email' => 'anna@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'anna@example.com')->first();

    expect($user->folders)->toHaveCount(1)
        ->and($user->folders->first()->name)->toBe("Anna's wedding");
});

test('default folder returns the existing folder', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    expect($user->defaultFolder()->is($folder))->toBeTrue()
        ->and($user->folders()->count())->toBe(1);
});

test('deleting your own account also deletes your image files', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('photo.jpg')->store('photos', 'public');
    Photo::factory()->for($user)->create(['image_path' => $path]);

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertRedirect('/');

    $this->assertModelMissing($user);
    Storage::disk('public')->assertMissing($path);
});

test('default folder creates a folder when the user has none', function () {
    $user = User::factory()->create(['name' => 'Ben']);

    $folder = $user->defaultFolder();

    expect($folder->name)->toBe("Ben's wedding")
        ->and($user->folders()->count())->toBe(1);
});
