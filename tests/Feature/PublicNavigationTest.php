<?php

use App\Models\User;

test('logged-in users find My folder among the navigation links and can log out', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('about'))
        ->assertSeeInOrder(['Home', 'Gallery', 'My folder', 'About', 'Contact'])
        ->assertSee('action="'.route('logout').'"', false)
        ->assertDontSee('>Log in<', false);
});

test('guests see Log in and Register instead of My folder', function () {
    $this->get(route('about'))
        ->assertDontSee('My folder')
        ->assertSee('Log in')
        ->assertSee('Register');
});
