<?php

namespace App\Http\Controllers\Userzone;

use App\Enums\PhotoCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Show the upload form.
     */
    public function create(): View
    {
        $categories = PhotoCategory::cases();

        return view('userzone.photos.create', compact('categories'));
    }

    /**
     * Store the uploaded photo and add it to the owner's folder.
     */
    public function store(StorePhotoRequest $request): RedirectResponse
    {
        $user = $request->user();

        $photo = $user->photos()->create([
            ...$request->safe()->except('image'),
            'image_path' => $request->file('image')->store('photos', 'public'),
        ]);

        $folder = $user->defaultFolder();
        $folder->photos()->attach($photo);

        return redirect()
            ->route('user.folders.show', $folder)
            ->with('success', 'Your photo was uploaded.');
    }
}
