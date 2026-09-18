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
            'passengers' => 'nullable|integer|min:1|max:9',
            'cabin_class' => 'nullable|in:economy,premium_economy,business,first',
            'airline' => 'nullable|string|max:255',
            'sort' => 'nullable|in:departure,arrival,price,duration',
        ]);

        $query = Flight::query()
            ->where('is_cancelled', false)
            ->where('is_archived', false)
            ->where('seats_available', '>', 0)
            ->whereIn('status', ['confirmed', 'checked_in', 'boarding'])
            ->where('departure_time', '>=', now())
            ->orderBy('departure_time');

        if (! empty($validated['origin'])) {
            $query->where('origin', $validated['origin']);
        }

        if (! empty($validated['destination'])) {
            $query->where('destination', $validated['destination']);
        }

        if (! empty($validated['date'])) {
            $query->whereDate('departure_time', $validated['date']);
        }

        if (! empty($validated['passengers'])) {
            $query->where('seats_available', '>=', $validated['passengers']);
        }

        if (! empty($validated['cabin_class'])) {
            $query->where('cabin_class', $validated['cabin_class']);
        }

        if (! empty($validated['airline'])) {
            $query->where('airline', $validated['airline']);
        }

        $sort = $validated['sort'] ?? 'departure';
        $query->orderBy(match ($sort) {
            'arrival' => 'arrival_time',
            'price' => 'price',
            'duration' => 'duration_minutes',
            default => 'departure_time',
        });

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
