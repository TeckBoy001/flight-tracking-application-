<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function destinations()
    {
        return view('pages.destinations');
    }

    public function travelInformation()
    {
        return view('pages.travel-information');
    }

    public function safety()
    {
        return view('pages.flight-safety');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
