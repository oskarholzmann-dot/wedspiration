<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\WeddingFolder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FolderController extends Controller
{
    /**
     * Show one of the user's wedding folders with its photos.
     */
    public function show(WeddingFolder $folder): View
    {
        Gate::authorize('view', $folder);

        $photos = $folder->photos()->latest()->get();

        return view('userzone.folders.show', compact('folder', 'photos'));
    }
}
