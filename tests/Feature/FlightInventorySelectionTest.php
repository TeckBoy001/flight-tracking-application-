<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlightInventorySelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_filter_and_sort_multiple_available_flights(): void
    {
        Flight::create([
            'flight_number' => 'INV101',
            'airline' => 'Northline',
            'origin' => 'JFK',
            'destination' => 'LAX',
            'departure_city' => 'New York',
            'arrival_city' => 'Los Angeles',
            'departure_time' => now()->addDay()->setTime(8, 0),
            'arrival_time' => now()->addDay()->setTime(11, 0),
            'price' => 450,
            'total_seats' => 100,
            'seats_available' => 100,
            'cabin_class' => 'business',
            'currency' => 'USD',
            'status' => 'confirmed',
        ]);

        $selected = Flight::create([
            'flight_number' => 'INV102',
            'airline' => 'Coastway',
            'origin' => 'JFK',
            'destination' => 'LAX',
            'departure_city' => 'New York',
            'arrival_city' => 'Los Angeles',
            'departure_time' => now()->addDay()->setTime(10, 0),
            'arrival_time' => now()->addDay()->setTime(13, 0),
            'price' => 220,
            'total_seats' => 20,
            'seats_available' => 4,
            'cabin_class' => 'economy',
            'currency' => 'USD',
            'status' => 'confirmed',
        ]);

        Flight::create([
            'flight_number' => 'INV103',
            'airline' => 'Archived Air',
            'origin' => 'JFK',
            'destination' => 'LAX',
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(3),
            'price' => 100,
            'total_seats' => 100,
            'seats_available' => 100,
            'status' => 'confirmed',
            'is_archived' => true,
        ]);

        $response = $this->get(route('flights.search', [
            'origin' => 'JFK',
            'destination' => 'LAX',
            'passengers' => 3,
            'cabin_class' => 'economy',
            'sort' => 'price',
        ]));

        $response->assertOk()
            ->assertSee('INV102')
            ->assertDontSee('INV101')
            ->assertDontSee('INV103');
        $this->assertSame($selected->id, $selected->fresh()->id);
    }

    public function test_admin_created_inventory_is_searchable_with_metadata_and_tracking_code(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.flights.store'), [
            'flight_number' => 'INV104',
            'airline' => 'Admin Managed Air',
            'origin' => 'SEA',
            'departure_city' => 'Seattle',
            'departure_country' => 'United States',
            'destination' => 'SFO',
            'arrival_city' => 'San Francisco',
            'arrival_country' => 'United States',
            'departure_time' => now()->addDays(2)->format('Y-m-d\\TH:i'),
            'arrival_time' => now()->addDays(2)->addHours(2)->format('Y-m-d\\TH:i'),
            'duration_minutes' => 120,
            'price' => 199,
            'currency' => 'eur',
            'cabin_class' => 'premium_economy',
            'total_seats' => 60,
            'status' => 'confirmed',
        ]);

        $flight = Flight::where('flight_number', 'INV104')->firstOrFail();

        $response->assertRedirect(route('admin.flights.index'));
        $this->assertSame('EUR', $flight->currency);
        $this->assertSame('premium_economy', $flight->cabin_class);
        $this->assertSame(120, $flight->duration_minutes);
        $this->assertNotEmpty($flight->tracking_code);

        $this->get(route('flights.search', ['origin' => 'SEA', 'destination' => 'SFO']))
            ->assertOk()
            ->assertSee('INV104')
            ->assertSee('Seattle')
            ->assertSee('EUR 199.00');
    }
}
