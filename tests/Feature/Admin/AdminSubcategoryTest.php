<?php

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
        ->assertSee('js/admin-subcategories.js');
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
