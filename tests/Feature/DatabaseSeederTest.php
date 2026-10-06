<?php

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the seeder creates the admin account', function () {
    $this->seed();

    $admin = User::where('email', 'admin@admin.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->is_admin)->toBeTrue()
        ->and(Hash::check('password', $admin->password))->toBeTrue()
        ->and($admin->folders)->not->toBeEmpty();
});

test('every seeded photo is in its owner\'s folder', function () {
    $this->seed();

    Photo::with('folders')->get()->each(function (Photo $photo) {
        expect($photo->folders->pluck('user_id'))->toContain($photo->user_id);
    });
});
