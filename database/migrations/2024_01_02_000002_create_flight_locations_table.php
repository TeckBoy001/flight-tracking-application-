<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lightweight, optional breadcrumb trail: one row per time the
        // admin updates a flight's current location. Not required for the
        // core feature to work, but cheap to keep and nice on the map.
        Schema::create('flight_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['flight_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_locations');
    }
};
