@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Bookings</h1>

    @if($bookings->isEmpty())
        <p class="text-gray-500">You have no bookings yet. <a href="{{ route('flights.search') }}" class="text-indigo-600 hover:underline">Find a flight</a>.</p>
    @else
        <div class="space-y-3">
            @foreach($bookings as $booking)
                <div class="bg-white border rounded-xl shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm text-gray-500">{{ $booking->booking_reference }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="font-medium">{{ $booking->flight->origin }} &rarr; {{ $booking->flight->destination }}</div>
                        <div class="text-sm text-gray-500">{{ $booking->flight->departure_time->format('M j, Y g:i A') }} &middot; {{ $booking->seats_booked }} seat(s)</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="font-bold">${{ number_format($booking->total_price, 2) }}</div>
                        <a href="{{ route('bookings.show', $booking) }}" class="text-indigo-600 hover:underline text-sm">Details</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection
