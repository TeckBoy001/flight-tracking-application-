<?php

namespace App\Http\Controllers;

use App\Models\Flight;

class HomeController extends Controller
{
    public function index()
    {
        $origins = Flight::query()->distinct()->orderBy('origin')->pluck('origin');
        $destinations = Flight::query()->distinct()->orderBy('destination')->pluck('destination');

        return view('home', compact('origins', 'destinations'));
    }
}
