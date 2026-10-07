<?php

use App\Enums\PhotoCategory;
use App\Models\Photo;

test('the welcome page shows the latest photos', function () {
    $photo = Photo::factory()->create(['title' => 'Peony bridal bouquet']);

    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('Wedspiration')
        ->assertSee('Peony bridal bouquet');
});

test('guests can see the gallery', function () {
    Photo::factory()->create(['title' => 'Rustic barn']);

    $this->get(route('gallery.index'))
        ->assertOk()
        ->assertSee('Rustic barn');
});

test('the gallery can be filtered by category', function () {
    Photo::factory()->create(['title' => 'Peony bouquet', 'category' => PhotoCategory::Flowers]);
    Photo::factory()->create(['title' => 'Castle garden', 'category' => PhotoCategory::Venue]);

    $this->get(route('gallery.index', ['category' => 'flowers']))
        ->assertOk()
        ->assertSee('Peony bouquet')
        ->assertDontSee('Castle garden');
});

test('an unknown category shows all photos', function () {
    Photo::factory()->create(['title' => 'Peony bouquet', 'category' => PhotoCategory::Flowers]);
    Photo::factory()->create(['title' => 'Castle garden', 'category' => PhotoCategory::Venue]);

    $this->get(route('gallery.index', ['category' => 'does-not-exist']))
        ->assertOk()
        ->assertSee('Peony bouquet')
        ->assertSee('Castle garden');
});

test('guests can see a single photo', function () {
    $photo = Photo::factory()->create(['title' => 'First dance']);

    $this->get(route('gallery.show', $photo))
        ->assertOk()
        ->assertSee('First dance')
        ->assertSee($photo->user->name);
});

test('a missing photo returns 404', function () {
    $this->get('/gallery/999')->assertNotFound();
});

test('the about and contact pages load', function () {
    $this->get(route('about'))->assertOk()->assertSee('About Wedspiration');
    $this->get(route('contact'))->assertOk()->assertSee('Contact');
});
