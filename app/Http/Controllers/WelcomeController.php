<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $latestPhotos = Photo::with('user')->newestFirst()->take(6)->get();

        return view('welcome', compact('latestPhotos'));
    }
}
