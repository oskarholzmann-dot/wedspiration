<?php

namespace App\Http\Controllers\Userzone;

use App\Enums\PhotoCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    public function store(StorePhotoRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $photo = $user->photos()->create([
            ...$request->safe()->except('image'),
            'image_path' => $request->file('image')->store('photos', 'public'),
        ]);

        $folder = $user->defaultFolder();
        $folder->photos()->attach($photo);

        // The multi-upload script sends one photo per request and expects JSON back
        if ($request->wantsJson()) {
            $request->session()->flash('success', 'Your photos were uploaded.');

            return response()->json([
                'title' => $photo->title,
                'redirect' => route('user.folders.show', $folder),
            ], 201);
        }

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
    public function destroy(Request $request, Photo $photo): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $photo);

        $photo->deleteImageFile();
        $photo->delete();

        // Delete button on a photo tile: remove the tile without reloading the page
        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        // Delete button on a tile without JavaScript: stay on the page it was clicked on
        if ($request->boolean('return_to_previous')) {
            return back()->with('success', 'Your photo was deleted.');
        }

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Your photo was deleted.');
    }
}
