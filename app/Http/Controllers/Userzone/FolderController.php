<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\WeddingFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FolderController extends Controller
{
    /**
     * Show one of the user's wedding folders with its photos.
     */
    public function show(WeddingFolder $folder): View
    {
        Gate::authorize('view', $folder);

        $photos = $folder->photos()->with('user')->newestFirst()->get();

        return view('userzone.folders.show', compact('folder', 'photos'));
    }

    /**
     * Show the form to edit one of the user's folders.
     */
    public function edit(WeddingFolder $folder): View
    {
        Gate::authorize('update', $folder);

        return view('userzone.folders.edit', compact('folder'));
    }

    /**
     * Save the folder's name, wedding date and notes.
     */
    public function update(UpdateFolderRequest $request, WeddingFolder $folder): RedirectResponse
    {
        $folder->update($request->validated());

        return redirect()
            ->route('user.folders.show', $folder)
            ->with('success', 'Your folder was updated.');
    }

    /**
     * Delete the folder together with the photos the owner uploaded into it.
     * Photos saved from other couples only lose the link; they stay with their owners.
     */
    public function destroy(WeddingFolder $folder): RedirectResponse
    {
        Gate::authorize('delete', $folder);

        $ownUploads = $folder->photos()->where('photos.user_id', $folder->user_id)->get();

        foreach ($ownUploads as $photo) {
            $photo->deleteImageFile();
            $photo->delete();
        }

        $folder->delete();

        return redirect()
            ->route('user.dashboard')
            ->with('success', "Your folder and {$ownUploads->count()} uploaded ".Str::plural('photo', $ownUploads->count()).' were deleted.');
    }
}
