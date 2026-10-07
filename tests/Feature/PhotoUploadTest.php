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
        ->assertSee('Upload photos')
        ->assertSee('multiple', false)
        ->assertSee('webkitdirectory', false)
        ->assertSee('Drag photos or whole folders here')
        ->assertSee('js/photo-upload.js');
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
            'title' => str_repeat('a', 300),
            'category' => 'not-a-category',
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors(['title', 'category', 'image']);

    expect(Photo::count())->toBe(0);
});

test('without a title and an image, both are reported missing', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('user.photos.store'), ['title' => ''])
        ->assertSessionHasErrors(['title', 'image']);
});

test('without a title, the title is taken from the file name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('user.photos.store'), [
        'image' => UploadedFile::fake()->image('rustic_barn-wedding.jpg'),
    ]);

    expect(Photo::first()->title)->toBe('Rustic barn wedding');
});

test('the multi-upload script gets a JSON answer per photo', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    foreach (['first.jpg', 'second.jpg', 'third.jpg'] as $name) {
        $this->actingAs($user)
            ->postJson(route('user.photos.store'), ['image' => UploadedFile::fake()->image($name)])
            ->assertCreated()
            ->assertJson(['redirect' => route('user.folders.show', $folder)]);
    }

    expect($folder->photos()->count())->toBe(3);
});

test('a failed upload from the script gets a JSON error', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('user.photos.store'), [
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('image');
});

test('images larger than 2 MB are rejected', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('user.photos.store'), [
            'title' => 'Huge photo',
            'image' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        ])
        ->assertSessionHasErrors('image');
});
