<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()
            ->bookings()
            ->with('flight')
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email',
            'seats_booked' => 'required|integer|min:1|max:9',
        ]);

        $booking = DB::transaction(function () use ($flight, $validated) {
            // Lock the row to avoid overselling seats under concurrent bookings.
            $lockedFlight = Flight::where('id', $flight->id)->lockForUpdate()->firstOrFail();

            if ($lockedFlight->is_cancelled) {
                abort(422, 'This flight has been cancelled and cannot be booked.');
            }

            if ($lockedFlight->seats_available < $validated['seats_booked']) {
                abort(422, 'Not enough seats available on this flight.');
            }

            $lockedFlight->decrement('seats_available', $validated['seats_booked']);

            return Booking::create([
                'user_id' => Auth::id(),
                'flight_id' => $lockedFlight->id,
                'booking_reference' => Booking::generateReference(),
                'passenger_name' => $validated['passenger_name'],
                'passenger_email' => $validated['passenger_email'],
                'seats_booked' => $validated['seats_booked'],
                'total_price' => $lockedFlight->price * $validated['seats_booked'],
                'status' => 'confirmed',
            ]);
        });

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', 'Booking confirmed! Your reference is ' . $booking->booking_reference);
    }

    public function show(Booking $booking)
    {
        $this->authorizeOwner($booking);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        $this->authorizeOwner($booking);

        if ($booking->status === 'confirmed') {
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'cancelled']);
                $booking->flight()->increment('seats_available', $booking->seats_booked);
            });
        }

        return redirect()->route('bookings.index')->with('status', 'Booking cancelled.');
    }

    protected function authorizeOwner(Booking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
