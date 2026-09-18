@extends('layouts.app')

@section('title', $flight->exists ? 'Edit Flight' : 'New Flight')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $flight->exists ? 'Edit Flight' : 'New Flight' }}</h1>
        @if($flight->exists)
            <a href="{{ route('admin.flights.bookings', $flight) }}" class="text-sm text-indigo-600 hover:underline">
                View bookings for this flight &rarr;
            </a>
        @endif
    </div>

    <form method="POST" action="{{ $flight->exists ? route('admin.flights.update', $flight) : route('admin.flights.store') }}" class="space-y-6">
        @csrf
        @if($flight->exists) @method('PUT') @endif

        {{-- Flight details --}}
        <div class="bg-white border rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-4">Flight details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Flight number</label>
                    <input type="text" name="flight_number" value="{{ old('flight_number', $flight->flight_number) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Airline</label>
                    <input type="text" name="airline" value="{{ old('airline', $flight->airline) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Origin (airport / city)</label>
                    <input type="text" name="origin" value="{{ old('origin', $flight->origin) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Destination (airport / city)</label>
                    <input type="text" name="destination" value="{{ old('destination', $flight->destination) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Mid-flight stops</label>
                    <textarea name="stops" rows="3" class="w-full border rounded-md px-3 py-2" placeholder="One stop per line or comma-separated">{{ old('stops', implode("\n", $flight->stops ?? [])) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Admins can update these while the flight is in transit. These are displayed as route waypoints and can be changed without altering the original departure point.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Departure time</label>
                    <input type="datetime-local" name="departure_time" value="{{ old('departure_time', optional($flight->departure_time)->format('Y-m-d\TH:i')) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Estimated arrival time</label>
                    <input type="datetime-local" name="arrival_time" value="{{ old('arrival_time', optional($flight->arrival_time)->format('Y-m-d\TH:i')) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Price</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $flight->price) }}" class="w-full border rounded-md px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Total seats</label>
                    <input type="number" min="1" name="total_seats" value="{{ old('total_seats', $flight->total_seats ?? 100) }}" class="w-full border rounded-md px-3 py-2" required>
                    @if($flight->exists)
                        <p class="text-xs text-gray-400 mt-1">Changing this does not automatically adjust seats already booked.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status & timeline --}}
        <div class="bg-white border rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-1">Status &amp; tracking timeline</h2>
            <p class="text-xs text-gray-400 mb-4">This is what customers see as the flight's progress on the tracking page.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Current stage</label>
                    <select name="status" class="w-full border rounded-md px-3 py-2">
                        @foreach(\App\Models\Flight::TIMELINE_STAGES as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $flight->status ?? 'confirmed') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-6 pb-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_delayed" value="1" @checked(old('is_delayed', $flight->is_delayed ?? false))>
                        Mark as delayed
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_cancelled" value="1" @checked(old('is_cancelled', $flight->is_cancelled ?? false))>
                        Mark as cancelled
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="tracking_paused" value="1" @checked(old('tracking_paused', $flight->tracking_paused ?? false))>
                        Pause live tracking
                    </label>
                </div>
            </div>
        </div>

        {{-- Route coordinates --}}
        <div class="bg-white border rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-1">Route coordinates</h2>
            <p class="text-xs text-gray-400 mb-4">Used to draw the fixed departure/destination points and route line on the customer map. Optional, but the map looks best with both set.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 text-xs font-medium text-gray-500">Departure point</div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                        <input type="number" step="any" name="departure_latitude" value="{{ old('departure_latitude', $flight->departure_latitude) }}" class="w-full border rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                        <input type="number" step="any" name="departure_longitude" value="{{ old('departure_longitude', $flight->departure_longitude) }}" class="w-full border rounded-md px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 text-xs font-medium text-gray-500">Destination point</div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                        <input type="number" step="any" name="arrival_latitude" value="{{ old('arrival_latitude', $flight->arrival_latitude) }}" class="w-full border rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                        <input type="number" step="any" name="arrival_longitude" value="{{ old('arrival_longitude', $flight->arrival_longitude) }}" class="w-full border rounded-md px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        {{-- Current location map --}}
        <div class="bg-white border rounded-xl shadow-sm p-6">
            @include('admin.flights._location_map', ['flight' => $flight])
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.flights.index') }}" class="px-4 py-2 rounded-md border hover:bg-gray-50">Cancel</a>
            <button class="bg-indigo-600 text-white rounded-md px-6 py-2 hover:bg-indigo-700">Save</button>
        </div>
    </form>
@endsection
