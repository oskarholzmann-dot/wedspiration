<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FolderController as AdminFolderController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\SubcategoryController as AdminSubcategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\FolderController;
use App\Http\Controllers\Userzone\PhotoController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Userzone\SavedPhotoController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */
Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/folders', [GalleryController::class, 'folders'])->name('gallery.folders');
Route::get('/gallery/folders/{folder}', [GalleryController::class, 'folder'])->name('gallery.folders.show');
Route::get('/gallery/{photo}', [GalleryController::class, 'show'])->name('gallery.show')->whereNumber('photo');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    Route::name('user.')->group(function () {
        // For the user's dashboard (after login)
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');
        Route::get('/folders/{folder}/edit', [FolderController::class, 'edit'])->name('folders.edit');
        Route::patch('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

        // Save any photo from the gallery into your own folder, or remove it again
        Route::post('/saved-photos/{photo}', [SavedPhotoController::class, 'store'])->name('saved-photos.store');
        Route::delete('/saved-photos/{photo}', [SavedPhotoController::class, 'destroy'])->name('saved-photos.destroy');

        Route::get('/photos/create', [PhotoController::class, 'create'])->name('photos.create');
        Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
        Route::get('/photos/{photo}/edit', [PhotoController::class, 'edit'])->name('photos.edit');
        Route::patch('/photos/{photo}', [PhotoController::class, 'update'])->name('photos.update');
        Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
    });

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
 * Admin routes (only for users with is_admin)
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::delete('photos', [AdminPhotoController::class, 'bulkDestroy'])->name('photos.bulk-destroy');

    // Subcategories are created, filled (drag and drop) and deleted right in the gallery
    Route::post('subcategories', [AdminSubcategoryController::class, 'store'])->name('subcategories.store');
    Route::delete('subcategories/{subcategory}', [AdminSubcategoryController::class, 'destroy'])->name('subcategories.destroy');
    Route::patch('photos/{photo}/subcategory', [AdminSubcategoryController::class, 'assign'])->name('photos.subcategory');
    Route::patch('photos/sort', [AdminSubcategoryController::class, 'sortMany'])->name('photos.sort');

    Route::resource('photos', AdminPhotoController::class);
    Route::resource('users', AdminUserController::class);
    Route::delete('folders', [AdminFolderController::class, 'bulkDestroy'])->name('folders.bulk-destroy');
    Route::resource('folders', AdminFolderController::class);
});
