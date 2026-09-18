<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        if (app()->environment(['local', 'testing']) || env('SEED_DEMO_DATA', false)) {
            $this->call([
                FlightSeeder::class,
                DemoBookingSeeder::class,
            ]);
        }
    }
}
