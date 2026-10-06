<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the about page.
     */
    public function about(): View
    {
        return view('pages.about');
    }

    /**
     * Show the contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }
}
