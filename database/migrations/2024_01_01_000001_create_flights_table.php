<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number')->unique();
            $table->string('airline');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('total_seats')->default(100);
            $table->unsignedInteger('seats_available')->default(100);
            // scheduled | delayed | cancelled | departed | arrived
            $table->string('status')->default('scheduled');
            $table->timestamps();

            $table->index(['origin', 'destination', 'departure_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
