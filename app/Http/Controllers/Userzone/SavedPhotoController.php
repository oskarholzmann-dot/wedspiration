<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedPhotoController extends Controller
{
    /**
     * Save any public photo into the user's own folder. The photo itself is not copied:
     * only a row in folder_photo is added, and the uploader stays its owner.
     */
    public function store(Request $request, Photo $photo): RedirectResponse|JsonResponse
    {
        $folder = $request->user()->defaultFolder();

        // Saving the same photo twice changes nothing
        $folder->photos()->syncWithoutDetaching([$photo->id]);

        return $this->respond($request, true, "Saved to {$folder->name}.");
    }

    /**
     * Remove a photo from the user's folder. The photo itself stays in the gallery.
     */
    public function destroy(Request $request, Photo $photo): RedirectResponse|JsonResponse
    {
        $folder = $request->user()->defaultFolder();

        $folder->photos()->detach($photo->id);

        return $this->respond($request, false, "Removed from {$folder->name}.");
    }

    /**
     * Answer with JSON for the save script, or go back with a message for a normal form.
     */
    private function respond(Request $request, bool $saved, string $message): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['saved' => $saved, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
