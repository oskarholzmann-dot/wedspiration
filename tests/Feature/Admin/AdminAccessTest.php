<?php

use App\Models\User;

test('guests are redirected from the admin area to the login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('regular users get a 403 in the admin area', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admins can see the admin dashboard', function () {
    User::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Users');
});

test('only admins see the admin link in the navigation', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('user.dashboard'))
        ->assertDontSee(route('admin.dashboard'));

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('user.dashboard'))
        ->assertSee(route('admin.dashboard'));
});
