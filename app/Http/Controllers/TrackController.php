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

        $reference = strtoupper(trim($validated['booking_reference']));

        $booking = Booking::with(['flight.locationHistory'])
            ->whereRaw('upper(booking_reference) = ?', [$reference])
            ->first();

        $flight = $booking?->flight;

        if (! $booking && str_starts_with($reference, 'FLY-')) {
            $flight = \App\Models\Flight::with('locationHistory')
                ->whereRaw('upper(tracking_code) = ?', [$reference])
                ->first();
        }

        return view('track', [
            'booking' => $booking,
            'flight' => $flight,
            'searched' => true,
            'searchedReference' => $reference,
        ]);
    }
}
