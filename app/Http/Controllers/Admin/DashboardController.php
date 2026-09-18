<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_flights' => Flight::count(),
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'revenue' => Booking::where('status', 'confirmed')->sum('total_price'),
        ];

        $recentBookings = Booking::with(['flight', 'user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
