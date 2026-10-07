<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PhotoCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PhotoRequest;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Display a list of all photos.
     */
    public function index(): View
    {
        $photos = Photo::with('user')->newestFirst()->paginate(15);

        return view('admin.photos.index', compact('photos'));
    }

    /**
     * Show the form for creating a new photo.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $categories = PhotoCategory::cases();

        return view('admin.photos.create', compact('users', 'categories'));
    }

    /**
     * Store a new photo for the chosen owner and add it to their folder.
     */
    public function store(PhotoRequest $request): RedirectResponse
    {
        $owner = User::findOrFail($request->validated('user_id'));

        $photo = $owner->photos()->create([
            ...$request->safe()->except(['user_id', 'image']),
            'image_path' => $request->file('image')->store('photos', 'public'),
        ]);

        $owner->defaultFolder()->photos()->attach($photo);

        return redirect()
            ->route('admin.photos.show', $photo)
            ->with('success', 'The photo was created.');
    }

    /**
     * Display one photo with its owner and folders.
     */
    public function show(Photo $photo): View
    {
        $photo->load('user', 'folders');

        return view('admin.photos.show', compact('photo'));
    }

    /**
     * Show the form for editing the photo.
     */
    public function edit(Photo $photo): View
    {
        $categories = PhotoCategory::cases();

        return view('admin.photos.edit', compact('photo', 'categories'));
    }

    /**
     * Update the photo, and replace the image file when a new one was uploaded.
     */
    public function update(PhotoRequest $request, Photo $photo): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $photo->deleteImageFile();
            $data['image_path'] = $request->file('image')->store('photos', 'public');
        }

        $photo->update($data);

        return redirect()
            ->route('admin.photos.show', $photo)
            ->with('success', 'The photo was updated.');
    }

    /**
     * Delete several photos at once (the ticked checkboxes in the list), image files included.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['integer', 'exists:photos,id'],
        ], [
            'photos.required' => 'Tick at least one photo to delete.',
        ]);

        $photos = Photo::whereIn('id', $validated['photos'])->get();

        foreach ($photos as $photo) {
            $photo->deleteImageFile();
            $photo->delete();
        }

        return redirect()
            ->route('admin.photos.index')
            ->with('success', $photos->count().' '.Str::plural('photo', $photos->count()).' deleted.');
    }

    /**
     * Delete the photo together with its image file.
     */
    public function destroy(Request $request, Photo $photo): RedirectResponse|JsonResponse
    {
        $photo->deleteImageFile();
        $photo->delete();

        // The delete button in the gallery removes the photo without reloading the page
        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        // Deleted from the gallery: go back there, not to the admin list
        if ($request->boolean('return_to_previous')) {
            return back()->with('success', 'The photo was deleted.');
        }

        return redirect()
            ->route('admin.photos.index')
            ->with('success', 'The photo was deleted.');
    }
}
