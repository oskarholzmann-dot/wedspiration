<?php

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\Subcategory;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('admins can create a subcategory from the gallery', function () {
    $this->actingAs($this->admin)
        ->from(route('gallery.index'))
        ->post(route('admin.subcategories.store'), ['name' => 'Boho'])
        ->assertRedirect(route('gallery.index'))
        ->assertSessionHas('success');

    expect(Subcategory::where('name', 'Boho')->exists())->toBeTrue();
});

test('subcategory names are required and unique', function () {
    Subcategory::factory()->create(['name' => 'Boho']);

    $this->actingAs($this->admin)->post(route('admin.subcategories.store'), ['name' => ''])->assertSessionHasErrors('name');
    $this->actingAs($this->admin)->post(route('admin.subcategories.store'), ['name' => 'Boho'])->assertSessionHasErrors('name');
});

test('dragging a photo onto a subcategory moves it there', function () {
    $subcategory = Subcategory::factory()->create(['name' => 'Golden hour']);
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->patchJson(route('admin.photos.subcategory', $photo), ['subcategory_id' => $subcategory->id])
        ->assertOk()
        ->assertJson(['message' => 'Moved to Golden hour.', 'counts' => [$subcategory->id => 1]]);

    expect($photo->fresh()->subcategory->is($subcategory))->toBeTrue();
});

test('a photo is in only one subcategory at a time', function () {
    [$first, $second] = Subcategory::factory()->count(2)->create();
    $photo = Photo::factory()->create(['subcategory_id' => $first->id]);

    $this->actingAs($this->admin)->patchJson(route('admin.photos.subcategory', $photo), ['subcategory_id' => $second->id]);

    expect($photo->fresh()->subcategory_id)->toBe($second->id)
        ->and($first->photos()->count())->toBe(0);
});

test('a group of photos can be moved into a subcategory at once', function () {
    $subcategory = Subcategory::factory()->create(['name' => 'Boho']);
    $photos = Photo::factory()->count(3)->create();
    $untouched = Photo::factory()->create();

    $this->actingAs($this->admin)
        ->patchJson(route('admin.photos.sort'), ['photos' => $photos->pluck('id'), 'subcategory_id' => $subcategory->id])
        ->assertOk()
        ->assertJson(['message' => '3 photos moved to Boho.', 'counts' => [$subcategory->id => 3]]);

    expect($untouched->fresh()->subcategory_id)->toBeNull();
});

test('a group of photos can be moved into a category at once', function () {
    $photos = Photo::factory()->count(2)->create(['category' => PhotoCategory::Dress]);

    $this->actingAs($this->admin)
        ->patchJson(route('admin.photos.sort'), ['photos' => $photos->pluck('id'), 'category' => 'flowers'])
        ->assertOk()
        ->assertJson(['message' => '2 photos moved to Flowers.']);

    $photos->each(fn ($photo) => expect($photo->fresh()->category)->toBe(PhotoCategory::Flowers));
});

test('moving a group needs photos and exactly one target', function () {
    $photo = Photo::factory()->create();
    $subcategory = Subcategory::factory()->create();

    $this->actingAs($this->admin)->patchJson(route('admin.photos.sort'), ['subcategory_id' => $subcategory->id])
        ->assertJsonValidationErrors('photos');
    $this->actingAs($this->admin)->patchJson(route('admin.photos.sort'), ['photos' => [$photo->id]])
        ->assertJsonValidationErrors(['subcategory_id', 'category']);
    $this->actingAs($this->admin)->patchJson(route('admin.photos.sort'), ['photos' => [$photo->id], 'subcategory_id' => $subcategory->id, 'category' => 'flowers'])
        ->assertJsonValidationErrors('subcategory_id');
    $this->actingAs($this->admin)->patchJson(route('admin.photos.sort'), ['photos' => [$photo->id], 'category' => 'cake'])
        ->assertJsonValidationErrors('category');
});

test('regular users cannot move groups of photos', function () {
    $photo = Photo::factory()->create();

    $this->actingAs(User::factory()->create())
        ->patchJson(route('admin.photos.sort'), ['photos' => [$photo->id], 'category' => 'flowers'])
        ->assertForbidden();
});

test('admins get the select button and category drop targets', function () {
    Photo::factory()->create();

    $this->actingAs($this->admin)->get(route('gallery.index'))
        ->assertSee('data-select-toggle', false)
        ->assertSee('data-category-drop="flowers"', false)
        ->assertSee('data-sort-url="'.route('admin.photos.sort').'"', false);

    $this->actingAs(User::factory()->create())->get(route('gallery.index'))
        ->assertDontSee('data-select-toggle', false)
        ->assertDontSee('data-category-drop', false);
});

test('admins can delete a subcategory and its photos stay', function () {
    $subcategory = Subcategory::factory()->create();
    $photo = Photo::factory()->create(['subcategory_id' => $subcategory->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.subcategories.destroy', $subcategory))
        ->assertRedirect(route('gallery.index'));

    $this->assertModelMissing($subcategory);
    $this->assertModelExists($photo);
});

test('regular users cannot create, delete or assign subcategories', function () {
    $user = User::factory()->create();
    $subcategory = Subcategory::factory()->create();
    $photo = Photo::factory()->create();

    $this->actingAs($user)->post(route('admin.subcategories.store'), ['name' => 'Mine'])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.subcategories.destroy', $subcategory))->assertForbidden();
    $this->actingAs($user)->patchJson(route('admin.photos.subcategory', $photo), ['subcategory_id' => $subcategory->id])->assertForbidden();

    expect($photo->fresh()->subcategory_id)->toBeNull();
});

test('the gallery can be filtered by subcategory', function () {
    $subcategory = Subcategory::factory()->create(['name' => 'Rustic']);
    Photo::factory()->create(['title' => 'In rustic', 'subcategory_id' => $subcategory->id]);
    Photo::factory()->create(['title' => 'Not in rustic']);

    $this->get(route('gallery.index', ['subcategory' => $subcategory->id]))
        ->assertOk()
        ->assertSee('In rustic')
        ->assertDontSee('Not in rustic');
});

test('only admins get drop targets and the create form in the gallery', function () {
    Subcategory::factory()->create(['name' => 'Rustic']);
    Photo::factory()->create();

    $this->get(route('gallery.index'))
        ->assertSee('Rustic')
        ->assertDontSee('data-subcategory-drop', false)
        ->assertDontSee('New subcategory');

    $this->actingAs($this->admin)->get(route('gallery.index'))
        ->assertSee('data-subcategory-drop', false)
        ->assertSee('data-assign-url', false)
        ->assertSee('New subcategory')
        ->assertSee('js/admin-sorting.js');
});

test('the admin photo form can set the subcategory too', function () {
    $subcategory = Subcategory::factory()->create(['name' => 'Vintage']);
    $photo = Photo::factory()->create();

    $this->actingAs($this->admin)->get(route('admin.photos.edit', $photo))->assertSee('Vintage');

    $this->actingAs($this->admin)->patch(route('admin.photos.update', $photo), [
        'title' => $photo->title,
        'subcategory_id' => $subcategory->id,
    ]);

    expect($photo->fresh()->subcategory_id)->toBe($subcategory->id);
});
