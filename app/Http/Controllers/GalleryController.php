<?php

namespace App\Http\Controllers;

use App\Enums\PhotoCategory;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Show all public photos, optionally filtered by category.
     */
    public function index(Request $request): View
    {
        // Unknown categories in the URL are simply ignored
        $activeCategory = PhotoCategory::tryFrom((string) $request->query('category'));

        $photos = Photo::with('user')
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->newestFirst()
            ->paginate(12)
            ->withQueryString();

        $categories = PhotoCategory::cases();

        return view('gallery.index', compact('photos', 'categories', 'activeCategory'));
    }

    /**
     * Show a single photo.
     */
    public function show(Photo $photo): View
    {
        $photo->load('user');

        return view('gallery.show', compact('photo'));
    }
}
