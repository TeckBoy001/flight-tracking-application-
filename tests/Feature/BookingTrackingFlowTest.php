<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTrackingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_and_track_a_flight(): void
    {
        $customer = User::factory()->create();
        $flight = Flight::create([
            'flight_number' => 'SB301',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'LAX',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(6),
            'price' => 250,
            'total_seats' => 50,
            'seats_available' => 50,
            'status' => 'confirmed',
            'departure_latitude' => 40.6413,
            'departure_longitude' => -73.7781,
            'arrival_latitude' => 33.9416,
            'arrival_longitude' => -118.4085,
            'current_latitude' => 0,
            'current_longitude' => 0,
            'current_location_label' => 'Equator crossing',
        ]);

        $response = $this->actingAs($customer)->post(route('bookings.store', $flight), [
            'passenger_name' => 'Test Passenger',
            'passenger_email' => 'passenger@example.com',
            'seats_booked' => 2,
        ]);

        $booking = Booking::firstOrFail();
        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertMatchesRegularExpression('/^[A-Z]{3}-\d{6}$/', $booking->booking_reference);
        $this->assertSame($flight->id, $booking->flight_id);
        $this->assertSame(48, $flight->fresh()->seats_available);

        $tracking = $this->post(route('track.lookup'), [
            'booking_reference' => strtolower($booking->booking_reference),
        ]);

        $tracking->assertOk()
            ->assertSee('Test Passenger')
            ->assertSee('Equator crossing')
            ->assertSee($flight->tracking_code);
    }

    public function test_admin_created_flight_gets_a_tracking_code_and_can_be_found_directly(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.flights.store'), [
            'flight_number' => 'SB302',
            'airline' => 'SkyBook Air',
            'origin' => 'SEA',
            'destination' => 'SFO',
            'departure_time' => now()->addDay()->format('Y-m-d\\TH:i'),
            'arrival_time' => now()->addDay()->addHours(2)->format('Y-m-d\\TH:i'),
            'price' => 180,
            'total_seats' => 40,
            'status' => 'confirmed',
            'current_latitude' => 47.4502,
            'current_longitude' => -122.3088,
            'current_location_label' => 'Seattle departure gate',
        ]);

        $flight = Flight::where('flight_number', 'SB302')->firstOrFail();
        $response->assertRedirect(route('admin.flights.index'));
        $this->assertMatchesRegularExpression('/^FLY-[A-Z]{3}-\d{6}$/', $flight->tracking_code);

        $this->post(route('track.lookup'), [
            'booking_reference' => $flight->tracking_code,
        ])->assertOk()->assertSee('SB302')->assertSee('Seattle departure gate');
    }

    public function test_invalid_tracking_code_returns_a_clear_error_state(): void
    {
        $this->post(route('track.lookup'), [
            'booking_reference' => 'not-a-real-code',
        ])->assertOk()->assertSee("We couldn't find a booking", false);
    }

    public function test_departed_flights_cannot_be_booked(): void
    {
        $customer = User::factory()->create();
        $flight = Flight::create([
            'flight_number' => 'SB303',
            'airline' => 'SkyBook Air',
            'origin' => 'ORD',
            'destination' => 'MIA',
            'departure_time' => now()->subHour(),
            'arrival_time' => now()->addHours(2),
            'price' => 180,
            'total_seats' => 40,
            'seats_available' => 40,
            'status' => 'in_transit',
        ]);

        $this->actingAs($customer)->from(route('flights.show', $flight))
            ->post(route('bookings.store', $flight), [
                'passenger_name' => 'Test Passenger',
                'passenger_email' => 'passenger@example.com',
                'seats_booked' => 1,
            ])
            ->assertStatus(422)
            ->assertSee('no longer available');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_admin_status_and_location_changes_are_visible_to_customer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();
        $flight = Flight::create([
            'flight_number' => 'SB304',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'ORD',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(3),
            'price' => 200,
            'total_seats' => 20,
            'seats_available' => 20,
            'status' => 'confirmed',
            'departure_latitude' => 40.6413,
            'departure_longitude' => -73.7781,
            'arrival_latitude' => 41.9742,
            'arrival_longitude' => -87.9073,
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'flight_id' => $flight->id,
            'booking_reference' => 'XYZ-123456',
            'passenger_name' => 'Tracking Passenger',
            'passenger_email' => 'tracking@example.com',
            'seats_booked' => 1,
            'total_price' => 200,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.flights.update', $flight), [
            'flight_number' => 'SB304',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'ORD',
            'departure_time' => now()->addDay()->format('Y-m-d\\TH:i'),
            'arrival_time' => now()->addDay()->addHours(3)->format('Y-m-d\\TH:i'),
            'price' => 200,
            'total_seats' => 20,
            'status' => 'in_transit',
            'current_latitude' => 39.7392,
            'current_longitude' => -104.9903,
            'current_location_label' => 'Denver airspace',
        ]);

        $response->assertRedirect(route('admin.flights.index'));

        $this->post(route('track.lookup'), [
            'booking_reference' => $booking->booking_reference,
        ])->assertOk()
            ->assertSee('In Transit')
            ->assertSee('Denver airspace')
            ->assertSee('Denver airspace', false);

        $this->assertDatabaseHas('flight_locations', [
            'flight_id' => $flight->id,
            'latitude' => '39.7392000',
            'longitude' => '-104.9903000',
        ]);
    }
}
