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

test('the owner sees the edit link on their photo', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($photo->user)
        ->get(route('gallery.show', $photo))
        ->assertSee('Edit or delete this photo');
});

test('other users do not see the edit link', function () {
    $photo = Photo::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('gallery.show', $photo))
        ->assertDontSee('Edit or delete this photo');
});

test('the owner can open the edit form', function () {
    $photo = Photo::factory()->create(['title' => 'Rustic barn']);

    $this->actingAs($photo->user)
        ->get(route('user.photos.edit', $photo))
        ->assertOk()
        ->assertSee('Rustic barn');
});

test('the owner can update the photo without a new image', function () {
    $photo = Photo::factory()->create();
    $oldPath = $photo->image_path;

    $this->actingAs($photo->user)
        ->patch(route('user.photos.update', $photo), [
            'title' => 'New title',
            'category' => PhotoCategory::Venue->value,
        ])
        ->assertRedirect(route('gallery.show', $photo))
        ->assertSessionHas('success');

    $photo->refresh();

    expect($photo->title)->toBe('New title')
        ->and($photo->category)->toBe(PhotoCategory::Venue)
        ->and($photo->image_path)->toBe($oldPath);
});

test('replacing the image deletes the old file', function () {
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('photos', 'public');
    $photo = Photo::factory()->create(['image_path' => $oldPath]);

    $this->actingAs($photo->user)->patch(route('user.photos.update', $photo), [
        'title' => $photo->title,
        'image' => UploadedFile::fake()->image('new.jpg'),
    ]);

    $photo->refresh();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($photo->image_path);
});

test('the update is validated', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($photo->user)
        ->patch(route('user.photos.update', $photo), ['title' => ''])
        ->assertSessionHasErrors('title');
});

test('the owner can delete the photo and its file', function () {
    $path = UploadedFile::fake()->image('photo.jpg')->store('photos', 'public');
    $photo = Photo::factory()->create(['image_path' => $path]);
    $folder = WeddingFolder::factory()->for($photo->user)->create();
    $folder->photos()->attach($photo);

    $this->actingAs($photo->user)
        ->delete(route('user.photos.destroy', $photo))
        ->assertRedirect(route('user.dashboard'));

    $this->assertModelMissing($photo);
    $this->assertDatabaseMissing('folder_photo', ['photo_id' => $photo->id]);
    Storage::disk('public')->assertMissing($path);
});

test('a user cannot edit, update or delete someone else\'s photo', function () {
    $photo = Photo::factory()->create(['title' => 'Not yours']);
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)->get(route('user.photos.edit', $photo))->assertForbidden();
    $this->actingAs($otherUser)->patch(route('user.photos.update', $photo), ['title' => 'Hacked'])->assertForbidden();
    $this->actingAs($otherUser)->delete(route('user.photos.destroy', $photo))->assertForbidden();

    expect($photo->fresh()->title)->toBe('Not yours');
});
