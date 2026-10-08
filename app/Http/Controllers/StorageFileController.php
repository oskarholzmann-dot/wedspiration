<?php

// AI-GENERATED (beyond course scope): serves uploads when storage:link is missing — written with Claude Code

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Safety net for uploaded photos when "php artisan storage:link" was not run.
 *
 * Normally public/storage is a link to storage/app/public and the web server sends the
 * files itself, so this controller is never reached. Without the link, the request
 * lands here and the file is sent from the public disk instead.
 */
class StorageFileController extends Controller
{
    /**
     * Send one file from the public disk.
     */
    public function __invoke(string $path): StreamedResponse
    {
        // Only files inside the public disk: no ".." to climb into other folders
        abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
