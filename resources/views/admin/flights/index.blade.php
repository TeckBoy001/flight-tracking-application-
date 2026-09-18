@extends('layouts.app')

@section('title', 'Manage Flights')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Flights</h1>
        <a href="{{ route('admin.flights.create') }}" class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">+ New Flight</a>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="p-3">Flight #</th>
                    <th class="p-3">Airline</th>
                    <th class="p-3">Route</th>
                    <th class="p-3">Departure</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Seats</th>
                    <th class="p-3">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($flights as $flight)
                    <tr>
                        <td class="p-3 font-mono">{{ $flight->flight_number }}</td>
                        <td class="p-3">{{ $flight->airline }}</td>
                        <td class="p-3">{{ $flight->origin }} &rarr; {{ $flight->destination }}</td>
                        <td class="p-3">{{ $flight->departure_time->format('M j, g:i A') }}</td>
                        <td class="p-3">${{ number_format($flight->price, 2) }}</td>
                        <td class="p-3">{{ $flight->seats_available }}/{{ $flight->total_seats }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $flight->statusColor() }}">{{ $flight->badgeLabel() }}</span>
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.flights.bookings', $flight) }}" class="text-gray-500 hover:underline">Bookings</a>
                            <a href="{{ route('admin.flights.edit', $flight) }}" class="text-indigo-600 hover:underline ml-2">Edit</a>
                            <form method="POST" action="{{ route('admin.flights.destroy', $flight) }}" class="inline" onsubmit="return confirm('Delete this flight?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $flights->links() }}
    </div>
@endsection
