<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\User;
use App\Models\WeddingFolder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show an overview of all content in the app.
     */
    public function __invoke(): View
    {
        $counts = [
            'users' => User::count(),
            'folders' => WeddingFolder::count(),
            'photos' => Photo::count(),
        ];

        return view('admin.dashboard', compact('counts'));
    }
}
