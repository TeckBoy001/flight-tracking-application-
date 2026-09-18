@extends('layouts.app')

@section('title', 'Track Flight')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #tracking-map { height: 340px; border-radius: 0.75rem; }
        @media (min-width: 768px) { #tracking-map { height: 420px; } }
    </style>
@endpush

@section('content')

    {{-- Hero / search --}}
    <div class="max-w-2xl mx-auto text-center mb-10">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-sky-950 text-white mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2.5 1.5V22l4-1 4 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Track your flight</h1>
        <p class="text-gray-500 mt-2">Enter your tracking code to view your flight status and current location.</p>

        <form method="POST" action="{{ route('track.lookup') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
            @csrf
            <input
                type="text"
                name="booking_reference"
                value="{{ $searchedReference ?? old('booking_reference') }}"
                placeholder="e.g. FLY-ABC-123456"
                class="flex-1 border rounded-lg px-4 py-3 text-center sm:text-left tracking-widest font-mono uppercase focus:outline-none focus:ring-2 focus:ring-sky-950"
                required
                autofocus
            >
            <button class="bg-sky-950 text-white rounded-lg px-6 py-3 font-medium hover:bg-slate-800 transition">
                Track Flight
            </button>
        </form>
    </div>

    @if(!empty($searched))
        @if($flight ?? $booking?->flight)
            @php
                $flight = $flight ?? $booking->flight;
                $stages = \App\Models\Flight::TIMELINE_STAGES;
                $currentIndex = $flight->timelineIndex();
            @endphp

            <div class="max-w-4xl mx-auto space-y-6">

                {{-- Status alerts --}}
                @if($flight->is_cancelled)
                    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm font-medium">
                        This flight has been cancelled. Please contact support if you have questions about your booking.
                    </div>
                @elseif($flight->tracking_paused)
                    <div class="bg-slate-100 border border-slate-200 text-slate-700 rounded-xl px-4 py-3 text-sm font-medium">
                        Live tracking is currently paused by the airline. The latest known location remains visible below.
                    </div>
                @elseif($flight->is_delayed)
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm font-medium">
                        This flight is currently delayed. Times below reflect the original schedule.
                    </div>
                @endif

                {{-- Flight status + map card --}}
                <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-sky-950 text-white px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-300">Flight</div>
                            <div class="text-xl font-semibold">{{ $flight->airline }} &middot; {{ $flight->flight_number }}</div>
                            <div class="text-slate-300 text-sm mt-1">{{ $flight->origin }} &rarr; {{ $flight->destination }}</div>
                        </div>
                        <span class="self-start sm:self-auto text-xs font-semibold uppercase tracking-wide px-3 py-1.5 rounded-full {{ $flight->statusColor() }}">
                            {{ $flight->badgeLabel() }}
                        </span>
                    </div>

                    <div class="p-4">
                        @if($flight->hasCurrentLocation() || $flight->hasRouteCoordinates())
                            <div id="tracking-map"></div>
                        @else
                            <div class="h-40 flex items-center justify-center text-gray-400 text-sm bg-gray-50 rounded-xl">
                                Live location not yet available for this flight.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Flight details --}}
                <div class="bg-white border rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Flight Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Departure</div>
                            <div class="font-semibold">{{ $flight->origin }}</div>
                            <div class="text-sm text-gray-500">{{ $flight->departure_time->format('M j, Y \a\t g:i A') }}</div>
                        </div>
                        <div class="sm:border-x sm:px-6">
                            <div class="text-xs text-gray-400 mb-1">Current Location</div>
                            <div class="font-semibold">{{ $flight->currentLocationDisplay() }}</div>
                            @if($flight->hasCurrentLocation())
                                <div class="text-sm text-gray-500">Updated {{ optional($flight->locationHistory->last())->recorded_at?->diffForHumans() ?? $flight->updated_at->diffForHumans() }}</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Destination</div>
                            <div class="font-semibold">{{ $flight->destination }}</div>
                            <div class="text-sm text-gray-500">Est. {{ $flight->arrival_time->format('M j, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-white border rounded-2xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-5">Tracking Timeline</h2>
                    <ol class="relative border-l-2 border-gray-100 ml-3 space-y-6">
                        @foreach($stages as $key => $label)
                            @php
                                $stepIndex = $loop->index;
                                $isComplete = $stepIndex < $currentIndex;
                                $isCurrent = $stepIndex === $currentIndex;
                            @endphp
                            <li class="ml-6">
                                <span @class([
                                    'absolute -left-[11px] flex items-center justify-center w-5 h-5 rounded-full ring-4 ring-white',
                                    'bg-green-500 text-white' => $isComplete,
                                    'bg-sky-950 text-white' => $isCurrent,
                                    'bg-gray-200' => !$isComplete && !$isCurrent,
                                ])>
                                    @if($isComplete)
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </span>
                                <div @class([
                                    'font-medium',
                                    'text-gray-900' => $isComplete || $isCurrent,
                                    'text-gray-400' => !$isComplete && !$isCurrent,
                                ])>
                                    {{ $label }}
                                    @if($isCurrent)
                                        <span class="ml-2 text-xs font-semibold text-sky-950 align-middle">&bull; Current</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Tracking code footer --}}
                <div class="bg-white border rounded-2xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Tracking Code</div>
                        <div class="font-mono text-lg font-bold tracking-widest">{{ $booking?->booking_reference ?? $flight->tracking_code }}</div>
                        @if($booking)
                            <div class="text-xs text-gray-500 mt-1">Flight code: <span class="font-mono">{{ $flight->tracking_code }}</span></div>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                            @if($booking)
                                Passenger: <span class="font-medium text-gray-800">{{ $booking->passenger_name }}</span>
                            @else
                                Flight tracking code: <span class="font-medium text-gray-800">{{ $flight->tracking_code }}</span>
                            @endif
                    </div>
                </div>
            </div>

            @if($flight->hasCurrentLocation() || $flight->hasRouteCoordinates())
                @push('scripts')
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                        (function () {
                            const depLat = {{ $flight->departure_latitude ?? 'null' }};
                            const depLng = {{ $flight->departure_longitude ?? 'null' }};
                            const arrLat = {{ $flight->arrival_latitude ?? 'null' }};
                            const arrLng = {{ $flight->arrival_longitude ?? 'null' }};
                            const curLat = {{ $flight->current_latitude ?? 'null' }};
                            const curLng = {{ $flight->current_longitude ?? 'null' }};
                            const trail = @json($flight->locationHistory->map(fn ($p) => [(float) $p->latitude, (float) $p->longitude]));

                            const hasCoordinates = value => value !== null && value !== undefined;
                            const hasCurrent = hasCoordinates(curLat) && hasCoordinates(curLng);
                            const hasDeparture = hasCoordinates(depLat) && hasCoordinates(depLng);
                            const hasArrival = hasCoordinates(arrLat) && hasCoordinates(arrLng);
                            const center = hasCurrent ? [curLat, curLng] : (hasDeparture ? [depLat, depLng] : [20, 0]);
                            if (typeof L === 'undefined') {
                                document.getElementById('tracking-map').innerHTML = '<div class="p-4 text-sm text-gray-500">The map could not load. Flight details remain available below.</div>';
                                return;
                            }

                            const map = L.map('tracking-map', { scrollWheelZoom: false }).setView(center, 4);

                            L.tileLayer(@json(config('services.map.tile_url')), {
                                attribution: @json(config('services.map.attribution')),
                                maxZoom: 18,
                            }).addTo(map);

                            const airportIcon = L.divIcon({
                                className: '',
                                html: '<div style="width:12px;height:12px;border-radius:9999px;background:#0b1220;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.3);"></div>',
                                iconSize: [12, 12],
                            });

                            const planeIcon = L.divIcon({
                                className: '',
                                html: '<div style="font-size:26px;line-height:1;transform:rotate(45deg);filter:drop-shadow(0 1px 2px rgba(0,0,0,.3));">&#9992;&#65039;</div>',
                                iconSize: [28, 28],
                                iconAnchor: [14, 14],
                            });

                            const bounds = [];

                            const originLabel = @json($flight->origin);
                            const destinationLabel = @json($flight->destination);

                            if (hasDeparture) {
                                L.marker([depLat, depLng], { icon: airportIcon }).addTo(map).bindTooltip(originLabel);
                                bounds.push([depLat, depLng]);
                            }
                            if (hasArrival) {
                                L.marker([arrLat, arrLng], { icon: airportIcon }).addTo(map).bindTooltip(destinationLabel);
                                bounds.push([arrLat, arrLng]);
                            }
                            if (hasDeparture && hasArrival) {
                                L.polyline([[depLat, depLng], [arrLat, arrLng]], { color: '#94a3b8', weight: 2, dashArray: '6 6' }).addTo(map);
                            }
                            if (trail.length > 1) {
                                L.polyline(trail, { color: '#1d4ed8', weight: 3, opacity: 0.6 }).addTo(map);
                            }
                            if (hasCurrent) {
                                L.marker([curLat, curLng], { icon: planeIcon }).addTo(map).bindTooltip('Current location', { permanent: false });
                                bounds.push([curLat, curLng]);
                            }

                            if (bounds.length > 1) {
                                map.fitBounds(bounds, { padding: [40, 40] });
                            } else if (bounds.length === 1) {
                                map.setView(bounds[0], 5);
                            }

                            setTimeout(function () { map.invalidateSize(); }, 200);
                        })();
                    </script>
                @endpush
            @endif
        @else
            <div class="max-w-md mx-auto text-center bg-white border rounded-2xl shadow-sm p-8">
                <div class="text-gray-400 text-sm">
                    We couldn't find a booking for <span class="font-mono font-semibold text-gray-700">{{ $searchedReference }}</span>.
                    Double-check the tracking code and try again.
                </div>
            </div>
        @endif
    @endif
@endsection
