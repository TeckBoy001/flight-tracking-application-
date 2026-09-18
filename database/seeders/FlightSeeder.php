<?php

namespace Database\Seeders;

use App\Models\Flight;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    /**
     * Rough airport coordinates, just enough to draw a believable route
     * and place the demo aircraft somewhere along it.
     */
    protected array $coordinates = [
        'JFK' => [40.6413, -73.7781],
        'LAX' => [33.9416, -118.4085],
        'ORD' => [41.9742, -87.9073],
        'MIA' => [25.7959, -80.2870],
        'SFO' => [37.6213, -122.3790],
        'SEA' => [47.4502, -122.3088],
    ];

    public function run(): void
    {
        $routes = [
            ['JFK', 'LAX', 'Aurora Air'],
            ['LAX', 'JFK', 'Aurora Air'],
            ['ORD', 'MIA', 'Skyline Airways'],
            ['MIA', 'ORD', 'Skyline Airways'],
            ['SFO', 'SEA', 'Pacific Wings'],
            ['SEA', 'SFO', 'Pacific Wings'],
        ];

        foreach ($routes as $i => [$origin, $destination, $airline]) {
            [$depLat, $depLng] = $this->coordinates[$origin];
            [$arrLat, $arrLng] = $this->coordinates[$destination];

            for ($day = 1; $day <= 3; $day++) {
                $flightNumber = sprintf('%s%03d', substr($airline, 0, 2), $i * 10 + $day);

                // Make the very first seeded flight (JFK -> LAX, day 1) a
                // nice live demo: already in the air, roughly midway along
                // its route, so the tracking page has something to show.
                $isDemoInTransit = $i === 0 && $day === 1;

                $attributes = [
                    'airline' => $airline,
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_time' => now()->addDays($day)->setTime(8 + $day, 30),
                    'arrival_time' => now()->addDays($day)->setTime(11 + $day, 45),
                    'price' => rand(120, 650),
                    'total_seats' => 100,
                    'seats_available' => 100,
                    'status' => $isDemoInTransit ? 'in_transit' : 'confirmed',
                    'is_delayed' => false,
                    'is_cancelled' => false,
                    'departure_latitude' => $depLat,
                    'departure_longitude' => $depLng,
                    'arrival_latitude' => $arrLat,
                    'arrival_longitude' => $arrLng,
                ];

                if ($isDemoInTransit) {
                    $attributes['departure_time'] = now()->subHours(3);
                    $attributes['arrival_time'] = now()->addHours(2);
                    $attributes['current_latitude'] = ($depLat + $arrLat) / 2;
                    $attributes['current_longitude'] = ($depLng + $arrLng) / 2;
                    $attributes['current_location_label'] = 'Over the Rocky Mountains';
                }

                $flight = Flight::updateOrCreate(['flight_number' => $flightNumber], $attributes);

                if ($isDemoInTransit) {
                    $flight->locationHistory()->firstOrCreate([
                        'latitude' => $attributes['current_latitude'],
                        'longitude' => $attributes['current_longitude'],
                    ], [
                        'recorded_at' => now(),
                    ]);
                }
            }
        }
    }
}
