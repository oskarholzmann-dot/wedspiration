<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

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

test('a user cannot see someone else\'s folder', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('user.folders.show', $folder))
        ->assertForbidden();
});
