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

test('names with quotes cannot break the delete confirmation', function () {
    WeddingFolder::factory()->create(['name' => "Oskar's \"big\" wedding"]);
    Photo::factory()->create(['title' => "Anna's bouquet"]);

    // The apostrophe must arrive escaped for JavaScript, or the confirm() script breaks and deletes without asking
    $apostrophe = trim(json_encode("'", JSON_HEX_APOS), '"');

    $this->actingAs($this->admin)->get(route('admin.folders.index'))
        ->assertSee('Oskar'.$apostrophe.'s', false);

    $this->actingAs($this->admin)->get(route('admin.photos.index'))->assertSee('Anna'.$apostrophe.'s', false);
});

test('the folder list has a delete button for every folder', function () {
    $folders = WeddingFolder::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.folders.index'));

    foreach ($folders as $folder) {
        $response->assertSee('action="'.route('admin.folders.destroy', $folder).'"', false);
    }
});

test('several folders can be deleted at once, their photos stay', function () {
    [$first, $second, $kept] = WeddingFolder::factory()->count(3)->create();
    $photo = Photo::factory()->for($first->user)->create();
    $first->photos()->attach($photo);

    $this->actingAs($this->admin)
        ->delete(route('admin.folders.bulk-destroy'), ['folders' => [$first->id, $second->id]])
        ->assertRedirect(route('admin.folders.index'))
        ->assertSessionHas('success', '2 folders deleted.');

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    $this->assertModelExists($kept);
    $this->assertModelExists($photo);
});

test('bulk delete can also delete the owners\' photos in those folders', function () {
    $folder = WeddingFolder::factory()->create();
    $ownPhoto = Photo::factory()->for($folder->user)->create();
    $savedPhoto = Photo::factory()->create();
    $folder->photos()->attach([$ownPhoto->id, $savedPhoto->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.folders.bulk-destroy'), ['folders' => [$folder->id], 'with_photos' => '1'])
        ->assertSessionHas('success', '1 folder deleted together with 1 photo.');

    $this->assertModelMissing($ownPhoto);
    $this->assertModelExists($savedPhoto);
});

test('bulk delete needs at least one folder', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.folders.bulk-destroy'), [])
        ->assertSessionHasErrors('folders');
});

test('regular users cannot bulk delete', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('admin.folders.bulk-destroy'), ['folders' => [$folder->id]])
        ->assertForbidden();

    $this->assertModelExists($folder);
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
