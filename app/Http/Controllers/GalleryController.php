<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Show all public photos.
     */
    public function index(): View
    {
        $photos = Photo::with('user')->latest()->paginate(12);

        return view('gallery.index', compact('photos'));
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
