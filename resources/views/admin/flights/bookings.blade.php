@extends('layouts.app')

@section('title', 'Bookings — ' . $flight->flight_number)

@section('content')
    <a href="{{ route('admin.flights.edit', $flight) }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to flight</a>

    <div class="flex items-center justify-between mt-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $flight->airline }} #{{ $flight->flight_number }}</h1>
            <p class="text-gray-500 text-sm">{{ $flight->origin }} &rarr; {{ $flight->destination }} &middot; {{ $flight->departure_time->format('M j, Y g:i A') }}</p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full {{ $flight->statusColor() }}">{{ $flight->badgeLabel() }}</span>
    </div>

    @if($bookings->isEmpty())
        <p class="text-gray-500">No bookings have been made on this flight yet.</p>
    @else
        <div class="bg-white border rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="p-3">Reference</th>
                        <th class="p-3">Passenger</th>
                        <th class="p-3">Booked by</th>
                        <th class="p-3">Seats</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($bookings as $booking)
                        <tr>
                            <td class="p-3 font-mono">{{ $booking->booking_reference }}</td>
                            <td class="p-3">{{ $booking->passenger_name }}<br><span class="text-xs text-gray-400">{{ $booking->passenger_email }}</span></td>
                            <td class="p-3">{{ $booking->user->name }}</td>
                            <td class="p-3">{{ $booking->seats_booked }}</td>
                            <td class="p-3">${{ number_format($booking->total_price, 2) }}</td>
                            <td class="p-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection
