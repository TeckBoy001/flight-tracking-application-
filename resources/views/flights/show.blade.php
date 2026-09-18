@extends('layouts.app')

@section('title', $flight->flight_number)

@section('content')
    <a href="{{ route('flights.search') }}" class="text-sm text-brand-700 hover:text-brand-800 font-medium">&larr; Back to results</a>

    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 mt-4 md:p-8">
        <div class="flex items-center gap-2 mb-3 flex-wrap">
            <span class="font-semibold text-lg text-slate-900">{{ $flight->airline }}</span>
            <span class="text-slate-400">#{{ $flight->flight_number }}</span>
            <span class="text-xs px-2.5 py-1 rounded-full {{ $flight->statusColor() }}">{{ $flight->badgeLabel() }}</span>
        </div>

        <div class="text-3xl font-black text-slate-900 mb-6">{{ $flight->origin }} &rarr; {{ $flight->destination }}</div>
        <div class="mb-6 rounded-2xl bg-brand-50 border border-brand-100 p-4 text-sm text-brand-900">
            Track this flight with code <span class="font-mono font-bold">{{ $flight->tracking_code }}</span> from the tracking page.
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Departure</div>
                <div class="font-semibold mt-1 text-slate-900">{{ $flight->departure_time->format('M j, Y g:i A') }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Arrival</div>
                <div class="font-semibold mt-1 text-slate-900">{{ $flight->arrival_time->format('M j, Y g:i A') }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Price / seat</div>
                <div class="font-semibold mt-1 text-slate-900">${{ number_format($flight->price, 2) }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Seats available</div>
                <div class="font-semibold mt-1 text-slate-900">{{ $flight->seats_available }} / {{ $flight->total_seats }}</div>
            </div>
        </div>

        @auth
            @if(! $flight->is_cancelled && $flight->seats_available > 0 && in_array($flight->status, ['confirmed', 'checked_in', 'boarding'], true) && $flight->departure_time->isFuture())
                <form method="POST" action="{{ route('bookings.store', $flight) }}" class="border-t border-slate-200 pt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1 text-slate-700">Passenger name</label>
                        <input type="text" name="passenger_name" value="{{ old('passenger_name', auth()->user()->name) }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1 text-slate-700">Passenger email</label>
                        <input type="email" name="passenger_email" value="{{ old('passenger_email', auth()->user()->email) }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 text-slate-700">Seats</label>
                        <input type="number" name="seats_booked" min="1" max="{{ min(9, $flight->seats_available) }}" value="1" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>
                    <div class="md:col-span-3 flex items-end">
                        <button class="bg-brand-500 text-white rounded-md px-6 py-2.5 font-semibold hover:bg-brand-600 transition">Confirm booking</button>
                    </div>
                </form>
            @else
                <p class="border-t border-slate-200 pt-6 text-red-600">This flight is not currently available for booking. Search for another upcoming flight.</p>
            @endif
        @else
            <div class="border-t border-slate-200 pt-6">
                <a href="{{ route('login') }}" class="text-brand-700 hover:text-brand-800 font-medium">Log in</a> to book this flight.
            </div>
        @endauth
    </div>
@endsection
