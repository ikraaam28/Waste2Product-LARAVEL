<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeaHouseController extends Controller
{
    public function index()
    {
        return view('teahouse.index');
    }

    public function about()
    {
        return view('teahouse.about');
    }

    public function products()
    {
        return view('teahouse.products');
    }

    public function store()
    {
        return view('teahouse.store');
    }

    public function contact()
    {
        return view('teahouse.contact');
    }

    public function blog()
    {
        return view('teahouse.blog');
    }

    public function testimonial()
    {
        return view('teahouse.testimonial');
    }

    public function feature()
    {
        return view('teahouse.feature');
    }
}
