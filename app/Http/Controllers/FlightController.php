<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'nullable|string',
            'destination' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $query = Flight::query()->orderBy('departure_time');

        if (! empty($validated['origin'])) {
            $query->where('origin', $validated['origin']);
        }

        if (! empty($validated['destination'])) {
            $query->where('destination', $validated['destination']);
        }

        if (! empty($validated['date'])) {
            $query->whereDate('departure_time', $validated['date']);
        }

        $flights = $query->paginate(10)->withQueryString();

        return view('flights.index', [
            'flights' => $flights,
            'filters' => $validated,
        ]);
    }

    public function show(Flight $flight)
    {
        return view('flights.show', compact('flight'));
    }
}
