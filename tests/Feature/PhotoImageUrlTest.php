<?php

use App\Models\Photo;

test('an external image path is used as the url', function () {
    $photo = Photo::factory()->make(['image_path' => 'https://picsum.photos/seed/abc/800/600']);

    expect($photo->image_url)->toBe('https://picsum.photos/seed/abc/800/600');
});

test('a seeded sample image points to the public images folder', function () {
    $photo = Photo::factory()->make(['image_path' => 'images/seed/flowers-1.jpg']);

    expect($photo->image_url)->toBe(asset('images/seed/flowers-1.jpg'))
        ->and($photo->isUploaded())->toBeFalse();
});

test('deleting a photo never deletes a seeded sample image', function () {
    $photo = Photo::factory()->create(['image_path' => 'images/seed/flowers-1.jpg']);

    $photo->deleteImageFile();

    expect(public_path('images/seed/flowers-1.jpg'))->toBeFile();
});

test('every seeded sample image exists', function () {
    foreach (Photo::factory()->count(24)->make() as $photo) {
        expect(public_path($photo->image_path))->toBeFile();
    }
});

test('an uploaded image path points to the public storage folder', function () {
    $photo = Photo::factory()->make(['image_path' => 'photos/abc.jpg']);

    expect($photo->image_url)->toEndWith('/storage/photos/abc.jpg');
});
