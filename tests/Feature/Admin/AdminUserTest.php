<?php

use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('regular users cannot reach the admin user routes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.users.destroy', $this->admin))->assertForbidden();

    $this->assertModelExists($this->admin);
});

test('index lists all users', function () {
    User::factory()->create(['name' => 'Anna Example']);

    $this->actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Anna Example');
});

test('show and edit display a user', function () {
    $user = User::factory()->create(['email' => 'anna@example.com']);

    $this->actingAs($this->admin)->get(route('admin.users.show', $user))->assertOk()->assertSee('anna@example.com');
    $this->actingAs($this->admin)->get(route('admin.users.edit', $user))->assertOk()->assertSee('anna@example.com');
    $this->actingAs($this->admin)->get(route('admin.users.create'))->assertOk();
});

test('store creates a user with a hashed password and a folder', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Ben',
            'email' => 'ben@example.com',
            'password' => 'secret-password',
            'is_admin' => '1',
        ])
        ->assertSessionHas('success');

    $ben = User::where('email', 'ben@example.com')->first();

    expect($ben->is_admin)->toBeTrue()
        ->and($ben->password)->not->toBe('secret-password')
        ->and(Hash::check('secret-password', $ben->password))->toBeTrue()
        ->and($ben->folders)->toHaveCount(1);
});

test('store is validated', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => '',
            'email' => $this->admin->email,
            'password' => '',
        ])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('update keeps the password when the field is empty', function () {
    $user = User::factory()->create();
    $oldHash = $user->password;

    $this->actingAs($this->admin)
        ->patch(route('admin.users.update', $user), [
            'name' => 'Renamed',
            'email' => $user->email,
            'password' => '',
            'is_admin' => '0',
        ])
        ->assertRedirect(route('admin.users.show', $user));

    $user->refresh();

    expect($user->name)->toBe('Renamed')
        ->and($user->password)->toBe($oldHash);
});

test('update can make a user admin', function () {
    $user = User::factory()->create();

    $this->actingAs($this->admin)->patch(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'is_admin' => '1',
    ]);

    expect($user->fresh()->is_admin)->toBeTrue();
});

test('admins cannot remove their own admin rights', function () {
    $this->actingAs($this->admin)->patch(route('admin.users.update', $this->admin), [
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'is_admin' => '0',
    ]);

    expect($this->admin->fresh()->is_admin)->toBeTrue();
});

test('destroy deletes the user with their folders, photos and files', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $folder = WeddingFolder::factory()->for($user)->create();
    $path = UploadedFile::fake()->image('photo.jpg')->store('photos', 'public');
    $photo = Photo::factory()->for($user)->create(['image_path' => $path]);

    $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertModelMissing($user);
    $this->assertModelMissing($folder);
    $this->assertModelMissing($photo);
    Storage::disk('public')->assertMissing($path);
});

test('admins cannot delete themselves', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $this->admin))
        ->assertSessionHasErrors('user');

    $this->assertModelExists($this->admin);
});
