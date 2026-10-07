<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests are redirected from a folder to the login', function () {
    $folder = WeddingFolder::factory()->create();

    $this->get(route('user.folders.show', $folder))->assertRedirect(route('login'));
});

test('the owner can see their folder with its photos', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create(['name' => 'Anna & Ben']);
    $folder->photos()->attach(Photo::factory()->for($user)->create(['title' => 'Wildflower arch']));

    $this->actingAs($user)
        ->get(route('user.folders.show', $folder))
        ->assertOk()
        ->assertSee('Anna &amp; Ben', false)
        ->assertSee('Wildflower arch');
});

test('the owner can rename their folder and add details', function () {
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    $this->actingAs($user)->get(route('user.folders.edit', $folder))->assertOk();

    $this->actingAs($user)
        ->patch(route('user.folders.update', $folder), [
            'name' => 'Inspo',
            'wedding_date' => '2027-06-12',
            'notes' => 'Natural light please',
        ])
        ->assertRedirect(route('user.folders.show', $folder))
        ->assertSessionHas('success');

    $folder->refresh();

    expect($folder->name)->toBe('Inspo')
        ->and($folder->wedding_date->format('Y-m-d'))->toBe('2027-06-12')
        ->and($folder->notes)->toBe('Natural light please');
});

test('the folder update is validated', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs($folder->user)
        ->patch(route('user.folders.update', $folder), ['name' => '', 'wedding_date' => 'soon'])
        ->assertSessionHasErrors(['name', 'wedding_date']);
});

test('a user cannot edit someone else\'s folder', function () {
    $folder = WeddingFolder::factory()->create(['name' => 'Not yours']);
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)->get(route('user.folders.edit', $folder))->assertForbidden();
    $this->actingAs($otherUser)->patch(route('user.folders.update', $folder), ['name' => 'Hacked'])->assertForbidden();

    expect($folder->fresh()->name)->toBe('Not yours');
});

test('deleting your folder deletes your uploads in it but not saved photos', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    $path = UploadedFile::fake()->image('mine.jpg')->store('photos', 'public');
    $myPhoto = Photo::factory()->for($user)->create(['image_path' => $path]);
    $savedPhoto = Photo::factory()->create();
    $folder->photos()->attach([$myPhoto->id, $savedPhoto->id]);

    $this->actingAs($user)
        ->delete(route('user.folders.destroy', $folder))
        ->assertRedirect(route('user.dashboard'))
        ->assertSessionHas('success', 'Your folder and 1 uploaded photo were deleted.');

    $this->assertModelMissing($folder);
    $this->assertModelMissing($myPhoto);
    $this->assertModelExists($savedPhoto);
    Storage::disk('public')->assertMissing($path);
});

test('after deleting the folder, the next upload creates a new one', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();

    $this->actingAs($user)->delete(route('user.folders.destroy', $folder));
    $this->actingAs($user)->post(route('user.photos.store'), ['image' => UploadedFile::fake()->image('new.jpg')]);

    expect($user->folders()->count())->toBe(1)
        ->and($user->folders()->first()->photos()->count())->toBe(1);
});

test('a user cannot delete someone else\'s folder', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('user.folders.destroy', $folder))
        ->assertForbidden();

    $this->assertModelExists($folder);
});

test('a user cannot see someone else\'s folder', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('user.folders.show', $folder))
        ->assertForbidden();
});
