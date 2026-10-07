<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FolderRequest;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FolderController extends Controller
{
    /**
     * Display a list of all wedding folders.
     */
    public function index(): View
    {
        $folders = WeddingFolder::with('user')->withCount('photos')->latest()->latest('id')->paginate(15);

        return view('admin.folders.index', compact('folders'));
    }

    /**
     * Show the form for creating a new folder.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.folders.create', compact('users'));
    }

    /**
     * Store a new folder for the chosen owner.
     */
    public function store(FolderRequest $request): RedirectResponse
    {
        $owner = User::findOrFail($request->validated('user_id'));

        $folder = $owner->folders()->create($request->safe()->except('user_id'));

        return redirect()
            ->route('admin.folders.show', $folder)
            ->with('success', 'The folder was created.');
    }

    /**
     * Display one folder with its owner and photos.
     */
    public function show(WeddingFolder $folder): View
    {
        $folder->load('user', 'photos');

        return view('admin.folders.show', compact('folder'));
    }

    /**
     * Show the form for editing the folder.
     */
    public function edit(WeddingFolder $folder): View
    {
        return view('admin.folders.edit', compact('folder'));
    }

    /**
     * Update the folder.
     */
    public function update(FolderRequest $request, WeddingFolder $folder): RedirectResponse
    {
        $folder->update($request->validated());

        return redirect()
            ->route('admin.folders.show', $folder)
            ->with('success', 'The folder was updated.');
    }

    /**
     * Delete several folders at once (the ticked checkboxes in the list).
     * Optionally also delete the photos their owners uploaded into them.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'folders' => ['required', 'array'],
            'folders.*' => ['integer', 'exists:wedding_folders,id'],
            'with_photos' => ['boolean'],
        ], [
            'folders.required' => 'Tick at least one folder to delete.',
        ]);

        $folders = WeddingFolder::whereIn('id', $validated['folders'])->get();
        $deletedPhotos = 0;

        foreach ($folders as $folder) {
            if ($request->boolean('with_photos')) {
                $ownUploads = $folder->photos()->where('photos.user_id', $folder->user_id)->get();

                foreach ($ownUploads as $photo) {
                    $photo->deleteImageFile();
                    $photo->delete();
                    $deletedPhotos++;
                }
            }

            $folder->delete();
        }

        $message = $folders->count().' '.Str::plural('folder', $folders->count()).' deleted'
            .($request->boolean('with_photos') ? " together with {$deletedPhotos} ".Str::plural('photo', $deletedPhotos) : '')
            .'.';

        return redirect()->route('admin.folders.index')->with('success', $message);
    }

    /**
     * Delete the folder. Its photos stay in the gallery.
     */
    public function destroy(WeddingFolder $folder): RedirectResponse
    {
        $folder->delete();

        return redirect()
            ->route('admin.folders.index')
            ->with('success', 'The folder was deleted.');
    }
}
