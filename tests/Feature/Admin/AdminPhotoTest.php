<?php

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->admin()->create();
});

test('regular users cannot reach any admin photo route', function () {
    $photo = Photo::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.photos.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.photos.create'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.photos.show', $photo))->assertForbidden();
    $this->actingAs($user)->get(route('admin.photos.edit', $photo))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.photos.destroy', $photo))->assertForbidden();

    $this->assertModelExists($photo);
});

test('index lists all photos of all users', function () {
    Photo::factory()->create(['title' => 'Photo of Anna']);
    Photo::factory()->create(['title' => 'Photo of Ben']);

    $this->actingAs($this->admin)
        ->get(route('admin.photos.index'))
        ->assertOk()
        ->assertSee('Photo of Anna')
        ->assertSee('Photo of Ben');
});

test('show displays a photo of any user', function () {
    $photo = Photo::factory()->create(['title' => 'Castle garden']);

    $this->actingAs($this->admin)
        ->get(route('admin.photos.show', $photo))
        ->assertOk()
        ->assertSee('Castle garden')
        ->assertSee($photo->user->email);
});

test('create shows the form with a list of owners', function () {
    $owner = User::factory()->create(['name' => 'Anna Owner']);

    $this->actingAs($this->admin)
        ->get(route('admin.photos.create'))
        ->assertOk()
        ->assertSee('Anna Owner');
});

test('store creates a photo for the chosen owner and adds it to their folder', function () {
    $owner = User::factory()->create();
    $folder = WeddingFolder::factory()->for($owner)->create();

    $this->actingAs($this->admin)
        ->post(route('admin.photos.store'), [
            'user_id' => $owner->id,
            'title' => 'Champagne tower',
            'category' => PhotoCategory::Reception->value,
            'image' => UploadedFile::fake()->image('tower.jpg'),
        ])
        ->assertSessionHas('success');

    $photo = Photo::first();

    expect($photo->user->is($owner))->toBeTrue()
        ->and($folder->photos->first()->is($photo))->toBeTrue();
    Storage::disk('public')->assertExists($photo->image_path);
});

test('store is validated', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.photos.store'), [
            'user_id' => 999,
            'title' => '',
        ])
        ->assertSessionHasErrors(['user_id', 'title', 'image']);

    expect(Photo::count())->toBe(0);
});

test('edit shows the form for any photo', function () {
    $photo = Photo::factory()->create(['title' => 'Lace sleeves']);

    $this->actingAs($this->admin)
        ->get(route('admin.photos.edit', $photo))
        ->assertOk()
        ->assertSee('Lace sleeves');
});

test('update changes a photo of any user', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->patch(route('admin.photos.update', $photo), ['title' => 'Edited by admin'])
        ->assertRedirect(route('admin.photos.show', $photo));

    expect($photo->fresh()->title)->toBe('Edited by admin');
});

test('update cannot change the owner', function () {
    $photo = Photo::factory()->create();
    $originalOwner = $photo->user_id;

    $this->actingAs($this->admin)
        ->patch(route('admin.photos.update', $photo), [
            'title' => 'Still the same owner',
            'user_id' => $this->admin->id,
        ])
        ->assertSessionHasErrors('user_id');

    expect($photo->fresh()->user_id)->toBe($originalOwner);
});

test('admins see a delete button on every photo in the gallery', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('gallery.index'))
        ->assertSee('data-admin-delete', false)
        ->assertSee('data-delete-url="'.route('admin.photos.destroy', $photo).'"', false)
        ->assertSee('data-lightbox-delete', false);
});

test('regular users and guests see no delete buttons in the gallery', function () {
    Photo::factory()->create();

    $this->get(route('gallery.index'))->assertDontSee('data-admin-delete', false);

    $this->actingAs(User::factory()->create())
        ->get(route('gallery.index'))
        ->assertDontSee('data-admin-delete', false)
        ->assertDontSee('data-delete-url', false)
        ->assertDontSee('data-lightbox-delete', false);
});

test('deleting from the gallery gets a JSON answer', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->deleteJson(route('admin.photos.destroy', $photo))
        ->assertOk()
        ->assertJson(['deleted' => true]);

    $this->assertModelMissing($photo);
});

test('the photo list has a delete button for every photo', function () {
    $photos = Photo::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.photos.index'));

    foreach ($photos as $photo) {
        $response->assertSee('action="'.route('admin.photos.destroy', $photo).'"', false);
    }
});

test('several photos can be deleted at once, files included', function () {
    $path = UploadedFile::fake()->image('upload.jpg')->store('photos', 'public');
    $first = Photo::factory()->create(['image_path' => $path]);
    $second = Photo::factory()->create();
    $kept = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.photos.bulk-destroy'), ['photos' => [$first->id, $second->id]])
        ->assertRedirect(route('admin.photos.index'))
        ->assertSessionHas('success', '2 photos deleted.');

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    $this->assertModelExists($kept);
    Storage::disk('public')->assertMissing($path);
});

test('bulk delete needs at least one photo and is admin only', function () {
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.photos.bulk-destroy'), [])
        ->assertSessionHasErrors('photos');

    $this->actingAs(User::factory()->create())
        ->delete(route('admin.photos.bulk-destroy'), ['photos' => [$photo->id]])
        ->assertForbidden();

    $this->assertModelExists($photo);
});

test('destroy deletes a photo of any user together with its file', function () {
    $path = UploadedFile::fake()->image('photo.jpg')->store('photos', 'public');
    $photo = Photo::factory()->create(['image_path' => $path]);

    $this->actingAs($this->admin)
        ->delete(route('admin.photos.destroy', $photo))
        ->assertRedirect(route('admin.photos.index'));

    $this->assertModelMissing($photo);
    Storage::disk('public')->assertMissing($path);
});
