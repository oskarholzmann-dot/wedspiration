<?php

use App\Models\Photo;

test('an external image path is used as the url', function () {
    $photo = Photo::factory()->make(['image_path' => 'https://picsum.photos/seed/abc/800/600']);

    expect($photo->image_url)->toBe('https://picsum.photos/seed/abc/800/600');
});

test('an uploaded image path points to the public storage folder', function () {
    $photo = Photo::factory()->make(['image_path' => 'photos/abc.jpg']);

    expect($photo->image_url)->toEndWith('/storage/photos/abc.jpg');
});
