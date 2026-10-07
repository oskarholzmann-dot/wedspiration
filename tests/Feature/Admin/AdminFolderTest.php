<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('regular users cannot reach the admin folder routes', function () {
    $folder = WeddingFolder::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.folders.index'))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.folders.destroy', $folder))->assertForbidden();

    $this->assertModelExists($folder);
});

test('index, show, create and edit pages load', function () {
    $folder = WeddingFolder::factory()->create(['name' => 'Anna & Ben']);

    $this->actingAs($this->admin)->get(route('admin.folders.index'))->assertOk()->assertSee('Anna &amp; Ben', false);
    $this->actingAs($this->admin)->get(route('admin.folders.show', $folder))->assertOk()->assertSee($folder->user->name);
    $this->actingAs($this->admin)->get(route('admin.folders.create'))->assertOk();
    $this->actingAs($this->admin)->get(route('admin.folders.edit', $folder))->assertOk()->assertSee('Anna &amp; Ben', false);
});

test('store creates a folder for the chosen owner', function () {
    $owner = User::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.folders.store'), [
            'user_id' => $owner->id,
            'name' => 'Summer wedding',
            'wedding_date' => '2027-06-12',
            'notes' => 'Outdoor ceremony',
        ])
        ->assertSessionHas('success');

    $folder = WeddingFolder::first();

    expect($folder->user->is($owner))->toBeTrue()
        ->and($folder->name)->toBe('Summer wedding')
        ->and($folder->wedding_date->format('Y-m-d'))->toBe('2027-06-12');
});

test('store is validated', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.folders.store'), [
            'user_id' => 999,
            'name' => '',
            'wedding_date' => 'not a date',
        ])
        ->assertSessionHasErrors(['user_id', 'name', 'wedding_date']);
});

test('update changes the folder but not the owner', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs($this->admin)
        ->patch(route('admin.folders.update', $folder), ['name' => 'Renamed'])
        ->assertRedirect(route('admin.folders.show', $folder));

    expect($folder->fresh()->name)->toBe('Renamed');

    $this->actingAs($this->admin)
        ->patch(route('admin.folders.update', $folder), ['name' => 'Renamed', 'user_id' => $this->admin->id])
        ->assertSessionHasErrors('user_id');
});

test('destroy deletes the folder but keeps its photos', function () {
    $folder = WeddingFolder::factory()->create();
    $photo = Photo::factory()->for($folder->user)->create();
    $folder->photos()->attach($photo);

    $this->actingAs($this->admin)
        ->delete(route('admin.folders.destroy', $folder))
        ->assertRedirect(route('admin.folders.index'));

    $this->assertModelMissing($folder);
    $this->assertModelExists($photo);
    $this->assertDatabaseMissing('folder_photo', ['folder_id' => $folder->id]);
});
