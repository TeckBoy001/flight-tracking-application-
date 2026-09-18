@extends('layouts.app')

@section('title', 'Flights')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Book a flight</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">Available flights</h1>
    </div>

    <form method="GET" action="{{ route('flights.search') }}" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="origin" value="{{ $filters['origin'] ?? '' }}" placeholder="Origin" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <input type="text" name="destination" value="{{ $filters['destination'] ?? '' }}" placeholder="Destination" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <button class="bg-brand-500 text-white rounded-md px-4 py-2.5 font-semibold hover:bg-brand-600 transition">Filter flights</button>
    </form>

    @if($flights->isEmpty())
        <p class="text-slate-500">No flights match your search.</p>
    @else
        <div class="space-y-4">
            @foreach($flights as $flight)
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="font-semibold text-slate-900">{{ $flight->airline }}</span>
                            <span class="text-slate-400 text-sm">#{{ $flight->flight_number }}</span>
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $flight->statusColor() }}">{{ $flight->badgeLabel() }}</span>
                        </div>
                        <div class="text-lg font-semibold text-slate-900">{{ $flight->origin }} &rarr; {{ $flight->destination }}</div>
                        <div class="text-sm text-slate-500 mt-1">
                            Departs {{ $flight->departure_time->format('M j, Y g:i A') }}
                            &middot; Arrives {{ $flight->arrival_time->format('M j, Y g:i A') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-2xl font-black text-slate-900">${{ number_format($flight->price, 2) }}</div>
                            <div class="text-xs text-slate-400">{{ $flight->seats_available }} seats left</div>
                        </div>
                        <a href="{{ route('flights.show', $flight) }}" class="bg-brand-500 text-white rounded-md px-4 py-2.5 font-semibold hover:bg-brand-600 whitespace-nowrap transition">View / Book</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $flights->links() }}
        </div>
    @endif
@endsection
