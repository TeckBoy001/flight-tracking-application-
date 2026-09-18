<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\FlightLocation;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index()
    {
        $flights = Flight::orderByDesc('departure_time')->paginate(15);

        return view('admin.flights.index', compact('flights'));
    }

    public function create()
    {
        $flight = new Flight();

        return view('admin.flights.form', compact('flight'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['seats_available'] = $validated['total_seats'];
        $validated['tracking_code'] = Flight::generateTrackingCode();

        $flight = Flight::create($validated);

        $this->recordLocationIfPresent($flight, $validated);

        return redirect()->route('admin.flights.index')->with('status', 'Flight created.');
    }

    public function edit(Flight $flight)
    {
        return view('admin.flights.form', compact('flight'));
    }

    public function update(Request $request, Flight $flight)
    {
        $validated = $this->validated($request, $flight);
        $confirmedSeats = (int) $flight->bookings()->where('status', 'confirmed')->sum('seats_booked');

        if ($validated['total_seats'] < $confirmedSeats) {
            return back()->withErrors([
                'total_seats' => 'Total seats cannot be lower than seats already booked.',
            ])->withInput();
        }

        $locationChanged = (string) $flight->current_latitude !== (string) ($validated['current_latitude'] ?? null)
            || (string) $flight->current_longitude !== (string) ($validated['current_longitude'] ?? null);

        if (array_key_exists('stops', $validated) && is_string($validated['stops'])) {
            $validated['stops'] = preg_split('/\r\n|\n|,/', $validated['stops']);
        }

        $validated['tracking_code'] = $flight->tracking_code ?: Flight::generateTrackingCode();

        $flight->update($validated);
        $flight->update(['seats_available' => $validated['total_seats'] - $confirmedSeats]);

        if ($locationChanged) {
            $this->recordLocationIfPresent($flight, $validated);
        }

        return redirect()->route('admin.flights.index')->with('status', 'Flight updated.');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return redirect()->route('admin.flights.index')->with('status', 'Flight deleted.');
    }

    /**
     * Admin view of every booking made against a single flight — lets an
     * admin see who's affected before changing a status or a location.
     */
    public function bookings(Flight $flight)
    {
        $bookings = $flight->bookings()->with('user')->latest()->paginate(15);

        return view('admin.flights.bookings', compact('flight', 'bookings'));
    }

    protected function recordLocationIfPresent(Flight $flight, array $validated): void
    {
        if (array_key_exists('current_latitude', $validated)
            && array_key_exists('current_longitude', $validated)
            && $validated['current_latitude'] !== null
            && $validated['current_longitude'] !== null
        ) {
            FlightLocation::create([
                'flight_id' => $flight->id,
                'latitude' => $validated['current_latitude'],
                'longitude' => $validated['current_longitude'],
                'recorded_at' => now(),
            ]);
        }
    }

    protected function validated(Request $request, ?Flight $flight = null): array
    {
        $flightId = $flight?->id;

        $validated = $request->validate([
            'flight_number' => 'required|string|max:50|unique:flights,flight_number,' . $flightId,
            'airline' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:origin',
            'stops' => 'nullable|string',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',

            'status' => 'required|in:' . implode(',', array_keys(Flight::TIMELINE_STAGES)),

            'departure_latitude' => 'nullable|numeric|between:-90,90',
            'departure_longitude' => 'nullable|numeric|between:-180,180',
            'arrival_latitude' => 'nullable|numeric|between:-90,90',
            'arrival_longitude' => 'nullable|numeric|between:-180,180',
            'current_latitude' => 'nullable|numeric|between:-90,90',
            'current_longitude' => 'nullable|numeric|between:-180,180',
            'current_location_label' => 'nullable|string|max:255',
        ]);

        // Checkboxes are absent from the request body when unchecked, so
        // read these separately rather than validating them as required.
        $validated['is_delayed'] = $request->boolean('is_delayed');
        $validated['is_cancelled'] = $request->boolean('is_cancelled');
        $validated['tracking_paused'] = $request->boolean('tracking_paused');

        return $validated;
    }
}
