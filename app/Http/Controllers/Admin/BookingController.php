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
            $oldSeatCount = $booking->seats_booked;
            $newSeatCount = (int) $validated['seats_booked'];
            $seatDifference = $newSeatCount - $oldSeatCount;

            if ($validated['status'] === 'cancelled' && $booking->status !== 'cancelled') {
                $booking->flight()->increment('seats_available', $oldSeatCount);
            }

            if ($validated['status'] !== 'cancelled' && $booking->status === 'cancelled') {
                $booking->flight()->decrement('seats_available', $oldSeatCount);
            }

            if ($booking->status === 'confirmed' && $validated['status'] === 'confirmed' && $seatDifference !== 0) {
                $booking->flight()->decrement('seats_available', $seatDifference);
            }

            if ($validated['status'] === 'confirmed' && $booking->status === 'confirmed' && $seatDifference < 0) {
                $booking->flight()->increment('seats_available', abs($seatDifference));
            }

            $booking->update([
                'passenger_name' => $validated['passenger_name'],
                'passenger_email' => $validated['passenger_email'],
                'seats_booked' => $newSeatCount,
                'status' => $validated['status'],
                'total_price' => $booking->flight->price * $newSeatCount,
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
                if ($validated['status'] === 'cancelled') {
                    $booking->flight()->increment('seats_available', $booking->seats_booked);
                } elseif ($booking->status === 'cancelled' && $validated['status'] === 'confirmed') {
                    $booking->flight()->decrement('seats_available', $booking->seats_booked);
                }

                $booking->update(['status' => $validated['status']]);
            });
        }

        return back()->with('status', 'Booking updated.');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->status === 'confirmed') {
            $booking->flight()->increment('seats_available', $booking->seats_booked);
        }

        $booking->delete();

        return back()->with('status', 'Booking removed.');
    }
}
