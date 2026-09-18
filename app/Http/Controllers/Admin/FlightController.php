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
        $query = Flight::query()->orderByDesc('departure_time');

        if (request()->filled('search')) {
            $search = request()->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('flight_number', 'like', "%{$search}%")
                    ->orWhere('airline', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request()->string('status'));
        }

        $flights = $query->paginate(15)->withQueryString();

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
            'departure_city' => 'nullable|string|max:255',
            'departure_country' => 'nullable|string|max:255',
            'arrival_city' => 'nullable|string|max:255',
            'arrival_country' => 'nullable|string|max:255',
            'stops' => 'nullable|string',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'cabin_class' => 'nullable|in:economy,premium_economy,business,first',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3|alpha',
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
        $validated['is_archived'] = $request->boolean('is_archived');
        $validated['tracking_paused'] = $request->boolean('tracking_paused');
        $validated['cabin_class'] = $validated['cabin_class'] ?? 'economy';
        $validated['currency'] = $validated['currency'] ?? 'USD';
        $validated['currency'] = strtoupper($validated['currency']);
        $validated['duration_minutes'] ??= $validated['departure_time'] && $validated['arrival_time']
            ? (int) round((strtotime($validated['arrival_time']) - strtotime($validated['departure_time'])) / 60)
            : null;

        return $validated;
    }
}
