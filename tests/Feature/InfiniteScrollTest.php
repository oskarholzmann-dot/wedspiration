<?php

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\WeddingFolder;

test('the gallery tells the scroll script where the next page is', function () {
    Photo::factory()->count(30)->create();

    $this->get(route('gallery.index'))
        ->assertOk()
        ->assertSee('data-infinite', false)
        ->assertSee('data-next="'.route('gallery.index', ['page' => 2]).'"', false)
        ->assertSee('js/infinite-scroll.js');
});

test('the second page returns the remaining photos and no further page', function () {
    $this->freezeTime();
    Photo::factory()->count(25)->create();
    $oldest = Photo::oldest('id')->first();

    $this->get(route('gallery.index', ['page' => 2]))
        ->assertOk()
        ->assertSee($oldest->title)
        ->assertSee('data-next=""', false);
});

test('the next page keeps the category filter', function () {
    Photo::factory()->count(30)->create(['category' => PhotoCategory::Flowers]);

    $this->get(route('gallery.index', ['category' => 'flowers']))
        ->assertSee('data-next="'.e(route('gallery.index', ['category' => 'flowers', 'page' => 2])).'"', false);
});

test('a short list has no next page', function () {
    Photo::factory()->count(3)->create();

    $this->get(route('gallery.index'))->assertSee('data-next=""', false);
});

test('folder pages scroll continuously too', function () {
    $folder = WeddingFolder::factory()->create();
    $folder->photos()->attach(Photo::factory()->count(30)->for($folder->user)->create());

    $this->get(route('gallery.folders.show', $folder))
        ->assertSee('data-next="'.route('gallery.folders.show', ['folder' => $folder, 'page' => 2]).'"', false);
});
