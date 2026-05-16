<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Latitude/Longitude stored for precision
            $table->decimal('clock_in_lat', 10, 7)->nullable()->after('clock_in');
            $table->decimal('clock_in_lng', 10, 7)->nullable()->after('clock_in_lat');
            $table->string('clock_in_address')->nullable()->after('clock_in_lng');

            $table->decimal('clock_out_lat', 10, 7)->nullable()->after('clock_out');
            $table->decimal('clock_out_lng', 10, 7)->nullable()->after('clock_out_lat');
            $table->string('clock_out_address')->nullable()->after('clock_out_lng');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_lat', 'clock_in_lng', 'clock_in_address',
                'clock_out_lat', 'clock_out_lng', 'clock_out_address',
            ]);
        });
    }
};
