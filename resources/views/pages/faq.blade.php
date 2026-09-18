@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
    <section class="mb-10 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Frequently asked questions</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900">Helpful answers for travelers.</h1>
    </section>

    <div class="space-y-6">
        @php
            $faqs = [
                ['q' => 'How do I book a flight?', 'a' => 'Use the search form on the homepage or flights page to find a route, review your selected flight, and complete the booking form.'],
                ['q' => 'Can I track my booking?', 'a' => 'Yes. Use the tracking page and enter your booking reference to view the current flight status, route details, and timeline.'],
                ['q' => 'What if I need to change my trip?', 'a' => 'Contact our support team for assistance with changes or trip updates. Policies may vary depending on the flight and ticket details.'],
                ['q' => 'How do I find my tracking code?', 'a' => 'Your booking reference is provided as part of the booking confirmation and appears in the booking details area.'],
                ['q' => 'What does the flight status mean?', 'a' => 'Status indicates the current travel stage, such as confirmed, in transit, approaching destination, or landed.'],
                ['q' => 'What should I do if I am delayed?', 'a' => 'Check the latest flight status, review any updates in the booking or tracking pages, and contact support if you need additional help.'],
                ['q' => 'What baggage information should I check?', 'a' => 'Review your booking details and carry-on guidance before departure. Baggage limits and rules can vary by itinerary and route.'],
                ['q' => 'How can I get support?', 'a' => 'Reach out through the contact page or support channels listed in the site footer for questions about tickets, status, or travel updates.'],
            ];
        @endphp

        @foreach ($faqs as $faq)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-900">{{ $faq['q'] }}</h2>
                <p class="mt-3 text-slate-600">{{ $faq['a'] }}</p>
            </div>
        @endforeach
    </div>
@endsection
