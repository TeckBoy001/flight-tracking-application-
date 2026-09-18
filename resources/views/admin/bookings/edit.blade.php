@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
    <div class="max-w-2xl">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-brand-700 hover:text-brand-800 font-medium">&larr; Back to bookings</a>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 mt-4 md:p-8">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 mb-6">Edit booking</h1>

            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                @csrf
                @method('PATCH')

                <div class="grid gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Booking reference</label>
                        <input value="{{ $booking->booking_reference }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 bg-slate-50" disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Passenger name</label>
                        <input type="text" name="passenger_name" value="{{ old('passenger_name', $booking->passenger_name) }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Passenger email</label>
                        <input type="email" name="passenger_email" value="{{ old('passenger_email', $booking->passenger_email) }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Seats booked</label>
                        <input type="number" name="seats_booked" min="1" max="9" value="{{ old('seats_booked', $booking->seats_booked) }}" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-200 rounded-md px-3 py-2.5 focus:border-brand-500 focus:outline-none" required>
                            <option value="confirmed" @selected(old('status', $booking->status) === 'confirmed')>Confirmed</option>
                            <option value="cancelled" @selected(old('status', $booking->status) === 'cancelled')>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="bg-brand-500 text-white rounded-md px-5 py-2.5 font-semibold hover:bg-brand-600 transition">Save changes</button>
                        <a href="{{ route('admin.bookings.index') }}" class="border border-slate-200 rounded-md px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
