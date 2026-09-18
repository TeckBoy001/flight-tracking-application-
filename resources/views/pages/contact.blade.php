@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <section class="max-w-5xl mx-auto">
        <div class="text-center mb-10">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Contact us</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">We’re here to help with your travel plans.</h1>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="rounded-3xl bg-slate-900 text-white p-8 shadow-xl">
                <h2 class="text-2xl font-bold">Support details</h2>
                <ul class="mt-6 space-y-4 text-slate-300">
                    <li><strong class="text-white">Email:</strong> support@skybook.example</li>
                    <li><strong class="text-white">Phone:</strong> +1 (800) 555-0147</li>
                    <li><strong class="text-white">Hours:</strong> 24/7 support</li>
                    <li><strong class="text-white">Best for:</strong> booking help, flight tracking, and travel questions</li>
                </ul>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900">Need a hand?</h2>
                <p class="mt-4 text-slate-600">Reach out before or during your trip for assistance with flight details, booking updates, or customer support questions.</p>
                <div class="mt-6 space-y-3">
                    <a href="mailto:support@skybook.example" class="block rounded-xl bg-brand-500 text-white px-4 py-3 text-center font-semibold hover:bg-brand-600">Email support</a>
                    <a href="{{ route('track.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-semibold text-slate-700 hover:border-brand-300 hover:text-brand-700">Track a booking</a>
                </div>
            </div>
        </div>
    </section>
@endsection
