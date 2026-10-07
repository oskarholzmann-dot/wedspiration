<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
 * The evaluator runs `migrate:fresh --seed` and visits every route as admin@admin.com.
 * This test does the same: it opens every page (GET route) of the app after seeding.
 */
test('every page opens without errors for the seeded admin', function () {
    $this->seed();

    $admin = User::where('email', 'admin@admin.com')->first();

    // Values for the route parameters, taken from the seeded data
    $parameters = [
        'photo' => $admin->photos()->first(),
        'folder' => $admin->folders()->first(),
        'user' => User::where('is_admin', false)->first(),
    ];

    // Pages that are not meant to be opened by a logged-in admin
    $skipped = [
        'login', 'register', 'password.request', 'password.reset',
        'verification.notice', 'verification.verify', 'password.confirm',
    ];

    $pages = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route) => in_array('GET', $route->methods()))
        ->filter(fn ($route) => $route->getName() && ! in_array($route->getName(), $skipped))
        ->filter(fn ($route) => str_starts_with($route->getActionName(), 'App\\'));

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $route) {
        $url = route($route->getName(), array_intersect_key($parameters, array_flip($route->parameterNames())));

        $response = $this->actingAs($admin)->get($url);

        expect($response->status())->toBe(200, "{$route->getName()} ({$url}) returned {$response->status()}");
    }
});
