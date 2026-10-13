<?php

// AI-GENERATED (beyond course scope): tests for an extra feature — written with Claude Code

use App\Models\WeddingFolder;

test('the welcome page has a WhatsApp link to itself', function () {
    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('https://wa.me/?text=', false)
        ->assertSee(rawurlencode(route('welcome')), false);
});

test('the public folder page shares its own link on WhatsApp', function () {
    $folder = WeddingFolder::factory()->create();

    $this->get(route('gallery.folders.show', $folder))
        ->assertOk()
        ->assertSee(rawurlencode(route('gallery.folders.show', $folder)), false);
});

test('the private folder page shares the public folder link, not its own', function () {
    $folder = WeddingFolder::factory()->create();

    $this->actingAs($folder->user)
        ->get(route('user.folders.show', $folder))
        ->assertOk()
        ->assertSee(rawurlencode(route('gallery.folders.show', $folder)), false)
        ->assertDontSee(rawurlencode(route('user.folders.show', $folder)), false);
});
