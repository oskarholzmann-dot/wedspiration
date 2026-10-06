<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

test('guests are redirected from the dashboard to the login', function () {
    $this->get(route('user.dashboard'))->assertRedirect(route('login'));
});

test('the dashboard welcomes the user and shows their folders', function () {
    $user = User::factory()->create(['name' => 'Anna']);
    $folder = WeddingFolder::factory()->for($user)->create(['name' => 'Anna & Ben']);
    $folder->photos()->attach(Photo::factory()->count(2)->for($user)->create());

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertOk()
        ->assertSee('Welcome, Anna')
        ->assertSee('Anna &amp; Ben', false)
        ->assertSee('2 photos');
});

test('the dashboard does not show folders of other users', function () {
    $user = User::factory()->create();
    WeddingFolder::factory()->create(['name' => 'Someone else']);

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertOk()
        ->assertDontSee('Someone else');
});
