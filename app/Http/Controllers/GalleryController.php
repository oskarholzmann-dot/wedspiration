<?php

namespace App\Http\Controllers;

use App\Enums\PhotoCategory;
use App\Models\Photo;
use App\Models\Subcategory;
use App\Models\WeddingFolder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Show all public photos, optionally filtered by category.
     */
    public function index(Request $request): View
    {
        // Unknown categories or subcategories in the URL are simply ignored
        $activeCategory = PhotoCategory::tryFrom((string) $request->query('category'));
        $activeSubcategory = Subcategory::find($request->integer('subcategory') ?: null);

        $photos = Photo::with('user')
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->when($activeSubcategory, fn ($query) => $query->whereBelongsTo($activeSubcategory))
            ->newestFirst()
            ->paginate(24)
            ->withQueryString();

        $categories = PhotoCategory::cases();
        $subcategories = Subcategory::withCount('photos')->orderBy('name')->get();

        return view('gallery.index', compact('photos', 'categories', 'activeCategory', 'subcategories', 'activeSubcategory'));
    }

    /**
     * Show a single photo with the folders it is in.
     */
    public function show(Photo $photo): View
    {
        $photo->load('user', 'folders');

        return view('gallery.show', compact('photo'));
    }

    /**
     * Show all folders that contain photos, each with a cover photo.
     */
    public function folders(): View
    {
        $folders = WeddingFolder::has('photos')
            ->with('user')
            ->withCount('photos')
            ->with(['photos' => fn ($query) => $query->newestFirst()->limit(1)])
            ->latest('id')
            ->paginate(24);

        return view('gallery.folders', compact('folders'));
    }

    /**
     * Show the photos of one folder. The notes for the photographer stay private.
     */
    public function folder(WeddingFolder $folder): View
    {
        $folder->load('user');
        $photos = $folder->photos()->with('user')->newestFirst()->paginate(24);

        return view('gallery.folder', compact('folder', 'photos'));
    }
}
