@extends('layouts.app')

@section('title', 'Manage Bookings')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Bookings</h1>

    <form method="GET" class="bg-white border rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3">
        <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Search reference..." class="border rounded-md px-3 py-2">
        <select name="status" class="border rounded-md px-3 py-2">
            <option value="">All statuses</option>
            <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
        </select>
        <button class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Filter</button>
    </form>

    <div class="bg-white border rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="p-3">Reference</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Flight</th>
                    <th class="p-3">Seats</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($bookings as $booking)
                    <tr>
                        <td class="p-3 font-mono">{{ $booking->booking_reference }}</td>
                        <td class="p-3">{{ $booking->user->name }}</td>
                        <td class="p-3">{{ $booking->flight->origin }} &rarr; {{ $booking->flight->destination }}</td>
                        <td class="p-3">{{ $booking->seats_booked }}</td>
                        <td class="p-3">${{ number_format($booking->total_price, 2) }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $booking->status === 'confirmed' ? 'cancelled' : 'confirmed' }}">
                                <button class="text-indigo-600 hover:underline ml-2">
                                    {{ $booking->status === 'confirmed' ? 'Cancel' : 'Restore' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Delete this booking permanently?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
@endsection
