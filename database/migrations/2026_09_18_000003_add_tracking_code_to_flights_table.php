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
            $table->string('tracking_code')->nullable()->unique()->after('flight_number');
        });

        DB::table('flights')->orderBy('id')->each(function ($flight): void {
            DB::table('flights')
                ->where('id', $flight->id)
                ->update(['tracking_code' => 'FLY-' . str_pad((string) $flight->id, 6, '0', STR_PAD_LEFT)]);
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropUnique(['tracking_code']);
            $table->dropColumn('tracking_code');
        });
    }
};
