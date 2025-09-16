<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function testimonial()
    {
        return view('pages.testimonial');
    }

    public function feature()
    {
        return view('pages.feature');
    }
}
