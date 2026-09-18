<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFlightStopsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_flight_stops_and_destination(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $flight = Flight::create([
            'flight_number' => 'AA101',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'LAX',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(7),
            'price' => 299.00,
            'total_seats' => 100,
            'seats_available' => 100,
            'status' => 'confirmed',
            'is_delayed' => false,
            'is_cancelled' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.flights.update', $flight), [
            'flight_number' => 'AA101',
            'airline' => 'SkyBook Air',
            'origin' => 'JFK',
            'destination' => 'SEA',
            'departure_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'arrival_time' => now()->addDay()->addHours(7)->format('Y-m-d\TH:i'),
            'price' => 299,
            'total_seats' => 100,
            'status' => 'in_transit',
            'stops' => "Boston\nDenver",
            'is_delayed' => false,
            'is_cancelled' => false,
        ]);

        $response->assertRedirect(route('admin.flights.index'));
        $this->assertSame(['Boston', 'Denver'], $flight->fresh()->stops);
        $this->assertSame('SEA', $flight->fresh()->destination);
    }
}
