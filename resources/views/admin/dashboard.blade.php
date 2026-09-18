@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <div class="flex gap-3 text-sm">
            <a href="{{ route('admin.flights.index') }}" class="text-indigo-600 hover:underline">Manage Flights</a>
            <a href="{{ route('admin.bookings.index') }}" class="text-indigo-600 hover:underline">Manage Bookings</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white border rounded-xl p-4">
            <div class="text-gray-400 text-xs">Total Flights</div>
            <div class="text-2xl font-bold">{{ $stats['total_flights'] }}</div>
        </div>
        <div class="bg-white border rounded-xl p-4">
            <div class="text-gray-400 text-xs">Total Bookings</div>
            <div class="text-2xl font-bold">{{ $stats['total_bookings'] }}</div>
        </div>
        <div class="bg-white border rounded-xl p-4">
            <div class="text-gray-400 text-xs">Confirmed</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['confirmed_bookings'] }}</div>
        </div>
        <div class="bg-white border rounded-xl p-4">
            <div class="text-gray-400 text-xs">Cancelled</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled_bookings'] }}</div>
        </div>
        <div class="bg-white border rounded-xl p-4">
            <div class="text-gray-400 text-xs">Revenue</div>
            <div class="text-2xl font-bold">${{ number_format($stats['revenue'], 2) }}</div>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Recent Bookings</h2>
    <div class="bg-white border rounded-xl shadow-sm divide-y">
        @forelse($recentBookings as $booking)
            <div class="p-4 flex items-center justify-between text-sm">
                <div>
                    <span class="font-mono text-gray-500">{{ $booking->booking_reference }}</span>
                    &middot; {{ $booking->user->name }}
                    &middot; {{ $booking->flight->origin }} &rarr; {{ $booking->flight->destination }}
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
        @empty
            <div class="p-4 text-gray-400 text-sm">No bookings yet.</div>
        @endforelse
    </div>
@endsection
