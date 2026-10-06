<?php

namespace App\Http\Controllers\Userzone;

use App\Enums\PhotoCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
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

    /**
     * Show the form to edit one of the user's photos.
     */
    public function edit(Photo $photo): View
    {
        Gate::authorize('update', $photo);

        $categories = PhotoCategory::cases();

        return view('userzone.photos.edit', compact('photo', 'categories'));
    }

    /**
     * Update the photo, and replace the image file when a new one was uploaded.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $photo->deleteImageFile();
            $data['image_path'] = $request->file('image')->store('photos', 'public');
        }

        $photo->update($data);

        return redirect()
            ->route('gallery.show', $photo)
            ->with('success', 'Your photo was updated.');
    }

    /**
     * Delete the photo together with its image file.
     */
    public function destroy(Photo $photo): RedirectResponse
    {
        Gate::authorize('delete', $photo);

        $photo->deleteImageFile();
        $photo->delete();

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Your photo was deleted.');
    }
}
