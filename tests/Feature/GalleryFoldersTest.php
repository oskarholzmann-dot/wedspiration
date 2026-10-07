<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;

test('the folder view shows one card per folder with photos', function () {
    $folder = WeddingFolder::factory()->create(['name' => 'Inspo']);
    $folder->photos()->attach(Photo::factory()->count(3)->for($folder->user)->create());
    WeddingFolder::factory()->create(['name' => 'Empty folder']);

    $this->get(route('gallery.folders'))
        ->assertOk()
        ->assertSee('Inspo')
        ->assertSee('3 photos')
        ->assertDontSee('Empty folder');
});

test('guests can open a folder and see its photos', function () {
    $folder = WeddingFolder::factory()->create(['name' => 'Inspo']);
    $folder->photos()->attach(Photo::factory()->for($folder->user)->create(['title' => 'Rustic barn']));

    $this->get(route('gallery.folders.show', $folder))
        ->assertOk()
        ->assertSee('Inspo')
        ->assertSee('Rustic barn')
        ->assertSee($folder->user->name);
});

test('the notes for the photographer stay private', function () {
    $folder = WeddingFolder::factory()->create(['notes' => 'Secret brief for our photographer']);
    $folder->photos()->attach(Photo::factory()->for($folder->user)->create());

    $this->get(route('gallery.folders.show', $folder))
        ->assertOk()
        ->assertDontSee('Secret brief for our photographer');
});

test('only the owner sees the edit button on the public folder page', function () {
    $folder = WeddingFolder::factory()->create();
    $folder->photos()->attach(Photo::factory()->for($folder->user)->create());

    $this->actingAs($folder->user)->get(route('gallery.folders.show', $folder))->assertSee('Edit folder');
    $this->actingAs(User::factory()->create())->get(route('gallery.folders.show', $folder))->assertDontSee('Edit folder');
});

test('a photo page links to the folders it is in', function () {
    $folder = WeddingFolder::factory()->create(['name' => 'Inspo']);
    $photo = Photo::factory()->for($folder->user)->create();
    $folder->photos()->attach($photo);

    $this->get(route('gallery.show', $photo))
        ->assertSee('Inspo')
        ->assertSee(route('gallery.folders.show', $folder));
});

test('both gallery views show the photos / folders switch', function () {
    $this->get(route('gallery.index'))->assertSee(route('gallery.folders'));
    $this->get(route('gallery.folders'))->assertSee(route('gallery.index'));
});
