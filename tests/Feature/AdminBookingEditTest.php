<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_booking_details(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $flight = Flight::create([
            'flight_number' => 'AA202',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'SFO',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(7),
            'price' => 249.00,
            'total_seats' => 80,
            'seats_available' => 80,
            'status' => 'confirmed',
            'is_delayed' => false,
            'is_cancelled' => false,
        ]);

        $booking = Booking::create([
            'user_id' => User::factory()->create()->id,
            'flight_id' => $flight->id,
            'booking_reference' => 'ABC-123456',
            'passenger_name' => 'Original Passenger',
            'passenger_email' => 'original@example.com',
            'seats_booked' => 1,
            'total_price' => 249.00,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.bookings.update', $booking), [
            'passenger_name' => 'Updated Passenger',
            'passenger_email' => 'updated@example.com',
            'seats_booked' => 2,
            'status' => 'confirmed',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertSame('Updated Passenger', $booking->fresh()->passenger_name);
        $this->assertSame('updated@example.com', $booking->fresh()->passenger_email);
        $this->assertSame(2, $booking->fresh()->seats_booked);
    }
}
