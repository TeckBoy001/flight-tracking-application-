@extends('layouts.app')

@section('title', 'About SkyBook')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-slate-900 text-white shadow-xl">
        <div class="grid lg:grid-cols-2 items-center">
            <div class="p-8 md:p-12 lg:p-16">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-300">About us</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight md:text-5xl">Travel designed around clarity, comfort, and confidence.</h1>
                <p class="mt-5 text-lg text-slate-300">SkyBook helps travelers book, manage, and track flights with a modern, straightforward experience. We focus on clear information, reliable communication, and a smooth journey from search to arrival.</p>
            </div>
            <div class="h-full min-h-[320px]">
                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80" alt="Passengers walking through an airport terminal" class="h-full w-full object-cover" />
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-8 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 h-11 w-11 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl">✈️</div>
            <h2 class="text-xl font-semibold">Simple booking</h2>
            <p class="mt-3 text-slate-600">Search flights, compare schedules, and complete a booking without unnecessary friction.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 h-11 w-11 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl">🧭</div>
            <h2 class="text-xl font-semibold">Clear information</h2>
            <p class="mt-3 text-slate-600">Get the details travelers need before they fly, including status and trip progress updates.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 h-11 w-11 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl">🛡️</div>
            <h2 class="text-xl font-semibold">Safety-first mindset</h2>
            <p class="mt-3 text-slate-600">We make safety guidance easy to access and encourage passengers to stay informed before travel.</p>
        </div>
    </section>

    <section class="mt-16 grid gap-10 lg:grid-cols-2 items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Who we serve</p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">Built for travelers who value straightforward planning.</h2>
            <p class="mt-4 text-slate-600">Whether a traveler is heading out for business, family time, or a quick city break, the experience should feel organized and reassuring. We design around the practical needs of modern passengers.</p>
            <ul class="mt-6 space-y-3 text-slate-700">
                <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Business travelers who need quick, dependable booking and status updates.</span></li>
                <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Families planning multi-stop or seasonal travel.</span></li>
                <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>Passengers who want clear communication before and during a trip.</span></li>
            </ul>
        </div>
        <img src="https://images.unsplash.com/photo-1529074963764-98f45c47344b?auto=format&fit=crop&w=1200&q=80" alt="Airport check-in station with travelers" class="rounded-3xl shadow-lg w-full h-full object-cover min-h-[420px]" />
    </section>
@endsection
