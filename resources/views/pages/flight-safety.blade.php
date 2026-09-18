@extends('layouts.app')

@section('title', 'Flight Safety')

@section('content')
    <section class="rounded-3xl bg-slate-900 text-white overflow-hidden shadow-xl">
        <div class="grid lg:grid-cols-2 items-center">
            <div class="p-8 md:p-12">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-300">Flight safety</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight md:text-5xl">Prepared travel starts with awareness.</h1>
                <p class="mt-5 text-lg text-slate-300">Aviation safety is built around preparation, communication, and attention to procedure. These are general guidance points to help passengers travel confidently and responsibly.</p>
            </div>
            <div class="h-full min-h-[320px]">
                <img src="https://images.unsplash.com/photo-1542296332-2e4473faf563?auto=format&fit=crop&w=1200&q=80" alt="Airplane wing on runway" class="h-full w-full object-cover" />
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Our approach</h2>
            <p class="mt-4 text-slate-600">We believe safe travel depends on transparent information, timely updates, and clear communication. While this project does not include company-specific aviation certifications or operational claims, it is designed to encourage passengers to stay informed and prepared.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Why safety matters</h2>
            <p class="mt-4 text-slate-600">Air travel is highly regulated and depends on professional procedures, standards, and communication from crew and staff. Safety awareness helps passengers arrive informed, calm, and ready for a secure journey.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Before the flight</h2>
            <p class="mt-4 text-slate-600">Review your itinerary, confirm flight details, pack appropriately, and arrive at the airport with enough time for check-in, screening, and boarding. Being prepared reduces stress and helps keep travel organized.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">At the airport</h2>
            <p class="mt-4 text-slate-600">Follow security instructions, keep travel documents accessible, and check gate updates carefully. Airport staff and signage are there to help passengers move through the experience safely and efficiently.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">During boarding</h2>
            <p class="mt-4 text-slate-600">Listen for boarding instructions, follow crew guidance, and keep your carry-on items organized. Boarding is a coordinated process designed to keep passengers and aircraft operations running smoothly.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">In flight</h2>
            <p class="mt-4 text-slate-600">Fasten your seatbelt when requested, pay attention to cabin crew guidance, and keep aisles and exits clear. Familiarize yourself with the nearest exit and emergency procedures as part of responsible travel.</p>
        </div>
    </section>
@endsection
