<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFlightTrackingPauseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_current_location_and_pause_tracking(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $flight = Flight::create([
            'flight_number' => 'DL204',
            'airline' => 'SkyBook Air',
            'origin' => 'SEA',
            'destination' => 'SFO',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(6),
            'price' => 319.00,
            'total_seats' => 90,
            'seats_available' => 90,
            'status' => 'in_transit',
            'is_delayed' => false,
            'is_cancelled' => false,
            'current_latitude' => 39.7392,
            'current_longitude' => -104.9903,
            'current_location_label' => 'Denver',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.flights.update', $flight), [
            'flight_number' => 'DL204',
            'airline' => 'SkyBook Air',
            'origin' => 'SEA',
            'destination' => 'SFO',
            'departure_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'arrival_time' => now()->addDay()->addHours(6)->format('Y-m-d\TH:i'),
            'price' => 319,
            'total_seats' => 90,
            'status' => 'in_transit',
            'stops' => "Boise",
            'is_delayed' => false,
            'is_cancelled' => false,
            'tracking_paused' => true,
            'current_latitude' => 37.7749,
            'current_longitude' => -122.4194,
            'current_location_label' => 'San Francisco',
        ]);

        $response->assertRedirect(route('admin.flights.index'));
        $this->assertTrue($flight->fresh()->tracking_paused);
        $this->assertSame('37.7749000', (string) $flight->fresh()->current_latitude);
        $this->assertSame('-122.4194000', (string) $flight->fresh()->current_longitude);
    }
}
