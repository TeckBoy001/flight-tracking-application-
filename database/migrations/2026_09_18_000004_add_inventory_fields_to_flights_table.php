<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->string('departure_city')->nullable()->after('origin');
            $table->string('departure_country')->nullable()->after('departure_city');
            $table->string('arrival_city')->nullable()->after('destination');
            $table->string('arrival_country')->nullable()->after('arrival_city');
            $table->unsignedInteger('duration_minutes')->nullable()->after('arrival_time');
            $table->string('cabin_class')->default('economy')->after('duration_minutes');
            $table->char('currency', 3)->default('USD')->after('price');
            $table->boolean('is_archived')->default(false)->after('is_cancelled');

            $table->index(['departure_city', 'arrival_city', 'cabin_class']);
            $table->index(['is_archived', 'departure_time']);
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropIndex(['departure_city', 'arrival_city', 'cabin_class']);
            $table->dropIndex(['is_archived', 'departure_time']);
            $table->dropColumn([
                'departure_city',
                'departure_country',
                'arrival_city',
                'arrival_country',
                'duration_minutes',
                'cabin_class',
                'currency',
                'is_archived',
            ]);
        });
    }
};
