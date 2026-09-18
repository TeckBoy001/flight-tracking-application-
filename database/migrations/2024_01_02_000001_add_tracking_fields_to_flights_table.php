<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            // Delay/cancellation are now flags layered on top of the journey
            // stage, rather than being stages themselves — this keeps the
            // progress timeline meaningful even when a flight is delayed.
            $table->boolean('is_delayed')->default(false)->after('status');
            $table->boolean('is_cancelled')->default(false)->after('is_delayed');

            // Fixed points used to draw the route on the map.
            $table->decimal('departure_latitude', 10, 7)->nullable()->after('is_cancelled');
            $table->decimal('departure_longitude', 10, 7)->nullable()->after('departure_latitude');
            $table->decimal('arrival_latitude', 10, 7)->nullable()->after('departure_longitude');
            $table->decimal('arrival_longitude', 10, 7)->nullable()->after('arrival_latitude');

            // Admin-controlled "where the plane is right now".
            $table->decimal('current_latitude', 10, 7)->nullable()->after('arrival_longitude');
            $table->decimal('current_longitude', 10, 7)->nullable()->after('current_latitude');
            $table->string('current_location_label')->nullable()->after('current_longitude');
        });

        // Migrate old status values into the new timeline-stage vocabulary
        // so existing rows keep making sense.
        DB::table('flights')->where('status', 'scheduled')->update(['status' => 'confirmed']);
        DB::table('flights')->where('status', 'arrived')->update(['status' => 'landed']);
        DB::table('flights')->where('status', 'delayed')->update(['status' => 'confirmed', 'is_delayed' => true]);
        DB::table('flights')->where('status', 'cancelled')->update(['status' => 'confirmed', 'is_cancelled' => true]);
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn([
                'is_delayed',
                'is_cancelled',
                'departure_latitude',
                'departure_longitude',
                'arrival_latitude',
                'arrival_longitude',
                'current_latitude',
                'current_longitude',
                'current_location_label',
            ]);
        });
    }
};
