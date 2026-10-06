<?php

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('guests cannot open the upload form', function () {
    $this->get(route('user.photos.create'))->assertRedirect(route('login'));
});

test('a user can open the upload form', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('user.photos.create'))
        ->assertOk()
        ->assertSee('Upload a photo');
});

test('uploading a photo stores it and adds it to the owner\'s folder', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    $response = $this->actingAs($user)->post(route('user.photos.store'), [
        'title' => 'Peony bouquet',
        'description' => 'Soft pink tones',
        'category' => PhotoCategory::Flowers->value,
        'image' => UploadedFile::fake()->image('bouquet.jpg'),
    ]);

    $photo = Photo::first();

    $response->assertRedirect(route('user.folders.show', $folder))
        ->assertSessionHas('success');

    expect($photo->title)->toBe('Peony bouquet')
        ->and($photo->category)->toBe(PhotoCategory::Flowers)
        ->and($photo->user->is($user))->toBeTrue()
        ->and($folder->photos->first()->is($photo))->toBeTrue();

    Storage::disk('public')->assertExists($photo->image_path);
});

test('the upload is validated', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('user.photos.store'), [
            'title' => '',
            'category' => 'not-a-category',
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors(['title', 'category', 'image']);

    expect(Photo::count())->toBe(0);
});

test('images larger than 2 MB are rejected', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('user.photos.store'), [
            'title' => 'Huge photo',
            'image' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        ])
        ->assertSessionHasErrors('image');
});
