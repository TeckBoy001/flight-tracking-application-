@extends('layouts.app')

@section('title', 'Booking ' . $booking->booking_reference)

@section('content')
    <a href="{{ route('bookings.index') }}" class="text-sm text-brand-700 hover:text-brand-800 font-medium">&larr; My bookings</a>

    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 mt-4 md:p-8">
        <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
            <div>
                <div class="text-slate-500 text-sm uppercase tracking-[0.2em]">Booking reference</div>
                <div class="text-2xl font-mono font-black text-slate-900">{{ $booking->booking_reference }}</div>
            </div>
            <span class="text-sm px-3 py-1.5 rounded-full {{ $booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($booking->status) }}
            </span>
        </div>

        <div class="border-t border-slate-200 pt-5 mb-6">
            <div class="font-semibold text-lg text-slate-900 mb-1">{{ $booking->flight->airline }} #{{ $booking->flight->flight_number }}</div>
            <div class="text-2xl font-bold text-slate-900">{{ $booking->flight->origin }} &rarr; {{ $booking->flight->destination }}</div>
            <div class="text-sm text-slate-500 mt-2">
                Departs {{ $booking->flight->departure_time->format('M j, Y g:i A') }}
                &middot; Flight status:
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $booking->flight->statusColor() }}">{{ ucfirst($booking->flight->status) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm border-t border-slate-200 pt-5">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Passenger</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $booking->passenger_name }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Email</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $booking->passenger_email }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Seats</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $booking->seats_booked }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-slate-500">Total paid</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $booking->flight->currency ?: 'USD' }} {{ number_format($booking->total_price, 2) }}</div>
            </div>
        </div>

        @if($booking->status === 'confirmed')
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="border-t border-slate-200 mt-6 pt-5" onsubmit="return confirm('Cancel this booking?');">
                @csrf
                <button class="text-red-600 hover:text-red-700 font-medium">Cancel this booking</button>
            </form>
        @endif
    </div>
@endsection
