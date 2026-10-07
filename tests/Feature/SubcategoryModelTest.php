<?php

use App\Models\Photo;
use App\Models\Subcategory;

test('a subcategory has many photos and a photo belongs to one subcategory', function () {
    $subcategory = Subcategory::factory()->create(['name' => 'Boho']);
    $photos = Photo::factory()->count(2)->create(['subcategory_id' => $subcategory->id]);

    expect($subcategory->photos)->toHaveCount(2)
        ->and($photos->first()->subcategory->is($subcategory))->toBeTrue();
});

test('deleting a subcategory keeps its photos', function () {
    $subcategory = Subcategory::factory()->create();
    $photo = Photo::factory()->create(['subcategory_id' => $subcategory->id]);

    $subcategory->delete();

    $this->assertModelExists($photo);
    expect($photo->fresh()->subcategory_id)->toBeNull();
});

test('the seeder creates subcategories with photos in them', function () {
    $this->seed();

    expect(Subcategory::count())->toBe(4)
        ->and(Photo::whereNotNull('subcategory_id')->count())->toBe(12);
});
