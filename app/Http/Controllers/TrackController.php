<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index()
    {
        return view('track');
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'booking_reference' => 'required|string',
        ]);

        $reference = trim($validated['booking_reference']);

        $booking = Booking::with(['flight.locationHistory'])
            ->where('booking_reference', $reference)
            ->first();

        return view('track', [
            'booking' => $booking,
            'searched' => true,
            'searchedReference' => $reference,
        ]);
    }
}
