<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

test('a user has many folders and photos', function () {
    $user = User::factory()->create();
    WeddingFolder::factory()->count(2)->for($user)->create();
    Photo::factory()->count(3)->for($user)->create();

    expect($user->folders)->toHaveCount(2)
        ->and($user->photos)->toHaveCount(3);
});

test('a folder and a photo belong to their user', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();
    $photo = Photo::factory()->for($user)->create();

    expect($folder->user->is($user))->toBeTrue()
        ->and($photo->user->is($user))->toBeTrue();
});

test('photos and folders are linked through the folder_photo table', function () {
    $folder = WeddingFolder::factory()->create();
    $photo = Photo::factory()->create();

    $folder->photos()->attach($photo);

    expect($folder->photos->first()->is($photo))->toBeTrue()
        ->and($photo->folders->first()->is($folder))->toBeTrue();

    $this->assertDatabaseHas('folder_photo', [
        'folder_id' => $folder->id,
        'photo_id' => $photo->id,
    ]);
});
