<?php

use App\Models\User;
use App\Models\WeddingFolder;

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

test('default folder creates a folder when the user has none', function () {
    $user = User::factory()->create(['name' => 'Ben']);

    $folder = $user->defaultFolder();

    expect($folder->name)->toBe("Ben's wedding")
        ->and($user->folders()->count())->toBe(1);
});
