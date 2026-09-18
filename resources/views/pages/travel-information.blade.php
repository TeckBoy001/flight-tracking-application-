@extends('layouts.app')

@section('title', 'Travel Information')

@section('content')
    <section class="mb-10 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Travel information</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">Helpful guidance before you fly.</h1>
        <p class="mt-4 mx-auto max-w-2xl text-slate-600">Preparing ahead makes any trip smoother. These are practical reminders and general travel tips for passengers using the booking and tracking experience.</p>
    </section>

    <section class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Before you travel</h2>
            <ul class="mt-5 space-y-3 text-slate-600">
                <li><strong class="text-slate-900">Check in early:</strong> Aim to arrive with enough time to complete security and boarding smoothly.</li>
                <li><strong class="text-slate-900">Review your booking:</strong> Confirm your flight number, departure time, and travel details before leaving for the airport.</li>
                <li><strong class="text-slate-900">Prepare documents:</strong> Keep identification and trip confirmation details easily accessible.</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">At the airport</h2>
            <ul class="mt-5 space-y-3 text-slate-600">
                <li><strong class="text-slate-900">Arrive with time to spare:</strong> Check airport guidance and expected wait times before heading out.</li>
                <li><strong class="text-slate-900">Security screening:</strong> Be ready for standard inspection procedures and follow staff directions.</li>
                <li><strong class="text-slate-900">Keep essentials nearby:</strong> Store travel documents, chargers, medications, and valuables in easy-to-reach bags.</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Boarding and baggage</h2>
            <ul class="mt-5 space-y-3 text-slate-600">
                <li><strong class="text-slate-900">Follow boarding calls:</strong> Please pay attention to gate announcements and boarding times.</li>
                <li><strong class="text-slate-900">Travel light when possible:</strong> Keep luggage within the limits provided by your travel arrangements.</li>
                <li><strong class="text-slate-900">Label baggage clearly:</strong> Ensure your bag is securely tagged and easy to identify.</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">In-flight and arrival</h2>
            <ul class="mt-5 space-y-3 text-slate-600">
                <li><strong class="text-slate-900">Follow crew instructions:</strong> Crew guidance helps keep passengers and aircraft operations safe and organized.</li>
                <li><strong class="text-slate-900">Check your status:</strong> Use the flight tracker to follow route progress and current flight information.</li>
                <li><strong class="text-slate-900">Plan your arrival:</strong> Review your destination arrival details before landing and prepare for onward travel.</li>
            </ul>
        </div>
    </section>
@endsection
