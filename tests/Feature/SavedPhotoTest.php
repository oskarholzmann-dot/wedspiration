<?php

// AI-GENERATED (beyond course scope): tests for an extra feature — written with Claude Code

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

test('guests cannot save photos', function () {
    $photo = Photo::factory()->create();

    $this->post(route('user.saved-photos.store', $photo))->assertRedirect(route('login'));
});

test('a user can save someone else\'s photo into their folder', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create(['name' => 'Inspo']);
    $photo = Photo::factory()->create();

    $this->actingAs($user)
        ->post(route('user.saved-photos.store', $photo))
        ->assertSessionHas('success', 'Saved to Inspo.');

    expect($folder->photos->first()->is($photo))->toBeTrue()
        ->and($photo->fresh()->user->is($user))->toBeFalse();
});

test('saving the same photo twice keeps it once', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();
    $photo = Photo::factory()->create();

    $this->actingAs($user)->post(route('user.saved-photos.store', $photo));
    $this->actingAs($user)->post(route('user.saved-photos.store', $photo));

    expect($folder->photos()->count())->toBe(1);
});

test('the save script gets a JSON answer', function () {
    $user = User::factory()->create();
    WeddingFolder::factory()->for($user)->create(['name' => 'Inspo']);
    $photo = Photo::factory()->create();

    $this->actingAs($user)
        ->postJson(route('user.saved-photos.store', $photo))
        ->assertOk()
        ->assertJson(['saved' => true, 'message' => 'Saved to Inspo.']);

    $this->actingAs($user)
        ->deleteJson(route('user.saved-photos.destroy', $photo))
        ->assertOk()
        ->assertJson(['saved' => false]);
});

test('removing a saved photo keeps the photo in the gallery', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();
    $photo = Photo::factory()->create();
    $folder->photos()->attach($photo);

    $this->actingAs($user)->delete(route('user.saved-photos.destroy', $photo));

    expect($folder->photos()->count())->toBe(0);
    $this->assertModelExists($photo);
});

test('saving only ever changes your own folder', function () {
    $user = User::factory()->create();
    WeddingFolder::factory()->for($user)->create();
    $otherFolder = WeddingFolder::factory()->create();
    $photo = Photo::factory()->create();
    $otherFolder->photos()->attach($photo);

    $this->actingAs($user)->delete(route('user.saved-photos.destroy', $photo));

    expect($otherFolder->photos()->count())->toBe(1);
});

test('tiles carry what the lightbox needs and the lightbox is on the page', function () {
    $photo = Photo::factory()->create(['title' => 'Peach rose bouquet']);

    $this->get(route('gallery.index'))
        ->assertSee('data-lightbox-src="'.$photo->image_url.'"', false)
        ->assertSee('data-title="Peach rose bouquet"', false)
        ->assertSee('data-page-url="'.route('gallery.show', $photo).'"', false)
        ->assertSee('data-lightbox hidden', false)
        ->assertSee('js/lightbox.js');
});

test('the photo page has a save button for logged-in users', function () {
    $user = User::factory()->create();
    WeddingFolder::factory()->for($user)->create();
    $photo = Photo::factory()->create();

    $this->actingAs($user)
        ->get(route('gallery.show', $photo))
        ->assertSee('action="'.route('user.saved-photos.store', $photo).'"', false);
});

test('logged-in users see save buttons and the drop zone, guests do not', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create(['name' => 'Inspo']);
    $saved = Photo::factory()->create(['title' => 'Already saved']);
    $folder->photos()->attach($saved);
    Photo::factory()->create(['title' => 'Not saved yet']);

    $this->get(route('gallery.index'))
        ->assertDontSee('data-save-form', false)
        ->assertDontSee('data-drop-zone', false);

    $this->actingAs($user)->get(route('gallery.index'))
        ->assertSee('data-save-form', false)
        ->assertSee('Drop here to save to Inspo')
        ->assertSee('data-saved="true"', false)
        ->assertSee('data-saved="false"', false);
});
