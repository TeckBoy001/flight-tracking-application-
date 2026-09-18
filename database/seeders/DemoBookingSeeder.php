<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoBookingSeeder extends Seeder
{
    /**
     * Creates a demo customer + a booking with a fixed, memorable tracking
     * code so you can try the /track page immediately after seeding
     * without needing to book a flight manually first.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) && ! env('SEED_DEMO_DATA', false)) {
            return;
        }

        $demoPassword = env('DEMO_PASSWORD');

        if (! $demoPassword) {
            return;
        }

        $customer = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make($demoPassword),
                'is_admin' => false,
            ]
        );

        $flight = Flight::where('status', 'in_transit')->first() ?? Flight::first();

        if (! $flight) {
            return;
        }

        Booking::updateOrCreate(
            ['booking_reference' => 'KOH-482951'],
            [
                'user_id' => $customer->id,
                'flight_id' => $flight->id,
                'passenger_name' => 'Jane Doe',
                'passenger_email' => 'demo@example.com',
                'seats_booked' => 1,
                'total_price' => $flight->price,
                'status' => 'confirmed',
            ]
        );
    }
}
