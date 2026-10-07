<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $folders = $request->user()
            ->folders()
            ->withCount('photos')
            ->with(['photos' => fn ($query) => $query->newestFirst()->limit(4)])
            ->get();

        return view('userzone.dashboard', compact('folders'));
    }
}
