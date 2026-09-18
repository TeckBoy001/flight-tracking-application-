@extends('layouts.app')

@section('title', 'Flights')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Book a flight</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">Available flights</h1>
    </div>

    <form method="GET" action="{{ route('flights.search') }}" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-8 gap-3">
        <input type="text" name="origin" value="{{ $filters['origin'] ?? '' }}" placeholder="Origin" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <input type="text" name="destination" value="{{ $filters['destination'] ?? '' }}" placeholder="Destination" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <input type="number" name="passengers" min="1" max="9" value="{{ $filters['passengers'] ?? 1 }}" placeholder="Passengers" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <select name="cabin_class" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
            <option value="">Any cabin</option>
            @foreach(['economy' => 'Economy', 'premium_economy' => 'Premium economy', 'business' => 'Business', 'first' => 'First'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['cabin_class'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="airline" value="{{ $filters['airline'] ?? '' }}" placeholder="Airline" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
        <select name="sort" class="border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
            <option value="departure" @selected(($filters['sort'] ?? 'departure') === 'departure')>Earliest departure</option>
            <option value="arrival" @selected(($filters['sort'] ?? '') === 'arrival')>Earliest arrival</option>
            <option value="price" @selected(($filters['sort'] ?? '') === 'price')>Lowest price</option>
            <option value="duration" @selected(($filters['sort'] ?? '') === 'duration')>Shortest duration</option>
        </select>
        <button class="bg-brand-500 text-white rounded-md px-4 py-2.5 font-semibold hover:bg-brand-600 transition">Search flights</button>
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
                        <div class="text-sm text-slate-500">{{ $flight->departure_city ?: $flight->origin }} to {{ $flight->arrival_city ?: $flight->destination }}</div>
                        <div class="text-sm text-slate-500 mt-1">
                            {{ $flight->departure_time->format('M j, Y g:i A') }} &rarr; {{ $flight->arrival_time->format('M j, Y g:i A') }}
                            @if($flight->duration_minutes) &middot; {{ floor($flight->duration_minutes / 60) }}h {{ $flight->duration_minutes % 60 }}m @endif
                        </div>
                        <div class="mt-2 text-xs uppercase tracking-wide text-slate-400">{{ str_replace('_', ' ', $flight->cabin_class ?: 'economy') }}</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-2xl font-black text-slate-900">{{ $flight->currency ?: 'USD' }} {{ number_format($flight->price, 2) }}</div>
                            <div class="text-xs text-slate-400">{{ $flight->seats_available }} seats left</div>
                        </div>
                        <a href="{{ route('flights.show', array_merge(['flight' => $flight], request()->query())) }}" class="bg-brand-500 text-white rounded-md px-4 py-2.5 font-semibold hover:bg-brand-600 whitespace-nowrap transition">Select flight</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $flights->links() }}
        </div>
    @endif
@endsection
