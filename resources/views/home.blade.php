@extends('layouts.app')

@section('title', 'Book Flights')

@section('content')
    <section class="overflow-hidden rounded-[32px] bg-slate-900 text-white shadow-2xl">
        <div class="grid lg:grid-cols-[1.3fr_0.7fr] items-center">
            <div class="p-8 md:p-10 lg:p-14">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-brand-300">Travel with confidence</p>
                <h1 class="mt-5 text-4xl font-black tracking-tight md:text-5xl lg:text-6xl">Fly smarter, travel better.</h1>
                <p class="mt-5 max-w-xl text-lg text-slate-300">SkyBook helps travelers book dependable flights, stay informed before takeoff, and track every step of the journey with clarity and ease.</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#search" class="rounded-md bg-brand-500 px-5 py-3 font-semibold text-white shadow-sm hover:bg-brand-600 transition">Book a flight</a>
                    <a href="{{ route('track.index') }}" class="rounded-md border border-white/20 bg-white/5 px-5 py-3 font-semibold text-white hover:bg-white/10 transition">Track your flight</a>
                </div>

                <div class="mt-10 flex flex-wrap gap-8 text-sm text-slate-200">
                    <div>
                        <div class="text-2xl font-black text-white">24/7</div>
                        <div>travel support</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">Clear</div>
                        <div>status updates</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">Simple</div>
                        <div>booking flow</div>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[420px]">
                <img src="https://images.unsplash.com/photo-1529074963764-98f45c47344b?auto=format&fit=crop&w=1200&q=80" alt="Travelers at an airport terminal" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-l from-slate-900/50 via-slate-900/15 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6 rounded-2xl bg-white/95 p-5 text-slate-900 shadow-xl backdrop-blur-sm">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-600">Popular route</div>
                    <div class="mt-3 flex items-center justify-between">
                        <div>
                            <div class="text-sm text-slate-500">From</div>
                            <div class="text-2xl font-black">JFK</div>
                        </div>
                        <div class="text-3xl text-brand-500">→</div>
                        <div>
                            <div class="text-sm text-slate-500">To</div>
                            <div class="text-2xl font-black">LAX</div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between text-sm text-slate-600">
                        <span>7h 45m</span>
                        <span>From $289</span>
                        <span class="text-emerald-600 font-semibold">On time</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-16 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-2xl text-brand-700">✈️</div>
            <h3 class="text-xl font-semibold text-slate-900">Professional booking</h3>
            <p class="mt-3 text-slate-600">Search routes, compare timings, and reserve your trip with a straightforward flow designed for modern travel.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-2xl text-brand-700">🧭</div>
            <h3 class="text-xl font-semibold text-slate-900">Real-time updates</h3>
            <p class="mt-3 text-slate-600">Track flight progress, current location, and route milestones from booking confirmation through touchdown.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-2xl text-brand-700">🛡️</div>
            <h3 class="text-xl font-semibold text-slate-900">Safety-first travel</h3>
            <p class="mt-3 text-slate-600">Access practical safety guidance and stay informed before, during, and after your journey.</p>
        </div>
    </section>

    <section id="search" class="mt-16 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-6 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Flight booking</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900">Search flights</h2>
            <p class="mt-2 text-slate-600">Choose your route and departure date to begin planning your next trip.</p>
        </div>

        <form method="GET" action="{{ route('flights.search') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="block text-sm font-medium mb-1 text-slate-700">Origin</label>
                <input list="origins" name="origin" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" placeholder="e.g. JFK">
                <datalist id="origins">
                    @foreach($origins as $o)
                        <option value="{{ $o }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-slate-700">Destination</label>
                <input list="destinations" name="destination" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" placeholder="e.g. LAX">
                <datalist id="destinations">
                    @foreach($destinations as $d)
                        <option value="{{ $d }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-slate-700">Date</label>
                <input type="date" name="date" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-slate-700">Passengers</label>
                <input type="number" name="passengers" min="1" max="9" value="1" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-slate-700">Cabin</label>
                <select name="cabin_class" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none">
                    <option value="">Any cabin</option>
                    <option value="economy">Economy</option>
                    <option value="premium_economy">Premium economy</option>
                    <option value="business">Business</option>
                    <option value="first">First</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-brand-500 text-white rounded-md px-4 py-2.5 font-semibold hover:bg-brand-600 transition">Search flights</button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('flights.search') }}" class="text-brand-700 hover:text-brand-800 font-medium">Browse all flights &rarr;</a>
        </div>
    </section>

    <section class="mt-16 grid gap-10 lg:grid-cols-2 items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Why travelers choose SkyBook</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900">A travel experience built around clarity and care.</h2>
            <p class="mt-4 text-slate-600">From the moment a trip is booked to the current status of a flight in transit, passengers need trustworthy information and an experience that feels calm, organized, and easy to navigate.</p>
            <ul class="mt-6 space-y-3 text-slate-700">
                <li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Transparent booking flow and easy route selection.</span></li>
                <li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Helpful travel updates before, during, and after departure.</span></li>
                <li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Dedicated resources for flight safety and practical travel guidance.</span></li>
            </ul>
        </div>
        <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80" alt="Airport travellers walking through a terminal" class="rounded-[28px] shadow-xl w-full h-full object-cover min-h-[420px]" />
    </section>

    <section class="mt-16 rounded-3xl bg-brand-50 border border-brand-100 p-6 md:p-8">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-700">Flight tracking</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900">Track your trip in real time.</h2>
                <p class="mt-4 text-slate-600">Use your tracking code to view flight status, current location, route information, and progress updates throughout the journey.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('track.index') }}" class="bg-brand-500 text-white px-5 py-3 rounded-md font-semibold hover:bg-brand-600 transition">Track a flight</a>
                    <a href="{{ route('flight-safety') }}" class="border border-brand-200 bg-white px-5 py-3 rounded-md font-semibold text-brand-700 hover:bg-brand-100 transition">Safety guidance</a>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tracking code</label>
                        <input value="" placeholder="e.g. FLY-ABC-123456" class="mt-2 w-full border border-slate-200 rounded-md px-3 py-2.5 font-mono tracking-[0.2em]" />
                    </div>
                    <button class="self-end bg-slate-900 text-white px-4 py-2.5 rounded-md font-semibold">Track</button>
                </div>
                <div class="mt-5 rounded-xl bg-slate-100 p-4">
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Current status</div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-lg font-semibold text-slate-900">In transit</span>
                        <span class="rounded-full bg-brand-100 text-brand-700 px-2.5 py-1 text-xs font-semibold">Live</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
