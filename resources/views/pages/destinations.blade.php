@extends('layouts.app')

@section('title', 'Destinations')

@section('content')
    <section class="mb-10">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Popular destinations</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">Fly where life takes you.</h1>
        <p class="mt-4 max-w-2xl text-slate-600">This platform supports the routes already available in the project, making it easy to search and book flights between key cities and travel hubs.</p>
    </section>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @php
            $destinations = [
                ['city' => 'New York', 'code' => 'JFK', 'image' => 'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?auto=format&fit=crop&w=1200&q=80'],
                ['city' => 'Los Angeles', 'code' => 'LAX', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80'],
                ['city' => 'Miami', 'code' => 'MIA', 'image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80'],
                ['city' => 'Chicago', 'code' => 'ORD', 'image' => 'https://images.unsplash.com/photo-1514565131-fce0801e5785?auto=format&fit=crop&w=1200&q=80'],
                ['city' => 'Seattle', 'code' => 'SEA', 'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80'],
                ['city' => 'San Francisco', 'code' => 'SFO', 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80'],
            ];
        @endphp

        @foreach ($destinations as $destination)
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <img src="{{ $destination['image'] }}" alt="{{ $destination['city'] }} skyline" class="h-56 w-full object-cover" />
                <div class="p-6">
                    <div class="text-sm uppercase tracking-[0.2em] text-brand-600">{{ $destination['code'] }}</div>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $destination['city'] }}</h2>
                    <p class="mt-3 text-slate-600">Well-connected routes for business, leisure, and family travel.</p>
                    <a href="{{ route('flights.search') }}" class="mt-5 inline-flex text-brand-700 font-semibold hover:text-brand-800">Book flights &rarr;</a>
                </div>
            </article>
        @endforeach
    </div>
@endsection
