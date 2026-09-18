<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['flight', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('reference')) {
            $query->where('booking_reference', 'like', '%' . $request->string('reference') . '%');
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['flight', 'user']);

        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'seats_booked' => 'required|integer|min:1|max:9',
            'status' => 'required|in:confirmed,cancelled',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $flight = $booking->flight()->lockForUpdate()->firstOrFail();
            $oldSeatCount = $booking->seats_booked;
            $newSeatCount = (int) $validated['seats_booked'];
            $wasConfirmed = $booking->status === 'confirmed';
            $willBeConfirmed = $validated['status'] === 'confirmed';
            $availableAfterRelease = $flight->seats_available + ($wasConfirmed ? $oldSeatCount : 0);

            if ($willBeConfirmed && $availableAfterRelease < $newSeatCount) {
                abort(422, 'Not enough seats available for this booking change.');
            }

            $newAvailable = $flight->seats_available;
            if ($wasConfirmed) {
                $newAvailable += $oldSeatCount;
            }
            if ($willBeConfirmed) {
                $newAvailable -= $newSeatCount;
            }
            $flight->update(['seats_available' => $newAvailable]);

            $booking->update([
                'passenger_name' => $validated['passenger_name'],
                'passenger_email' => $validated['passenger_email'],
                'seats_booked' => $newSeatCount,
                'status' => $validated['status'],
                'total_price' => $flight->price * $newSeatCount,
            ]);
        });

        return redirect()->route('admin.bookings.index')->with('status', 'Booking updated.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled',
        ]);

        if ($validated['status'] !== $booking->status) {
            DB::transaction(function () use ($booking, $validated) {
                $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
                $flight = $booking->flight()->lockForUpdate()->firstOrFail();

                if ($validated['status'] === 'confirmed' && $flight->seats_available < $booking->seats_booked) {
                    abort(422, 'Not enough seats available to restore this booking.');
                }

                if ($validated['status'] === 'cancelled') {
                    $flight->increment('seats_available', $booking->seats_booked);
                } else {
                    $flight->decrement('seats_available', $booking->seats_booked);
                }

                $booking->update(['status' => $validated['status']]);
            });
        }

        return back()->with('status', 'Booking updated.');
    }

    public function destroy(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $flight = $booking->flight()->lockForUpdate()->firstOrFail();

            if ($booking->status === 'confirmed') {
                $flight->increment('seats_available', $booking->seats_booked);
            }

            $booking->delete();
        });

        return back()->with('status', 'Booking removed.');
    }
}
