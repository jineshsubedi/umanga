<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('modules')->nullable()->after('departments');
        });

        // Set default value for existing rows
        \Illuminate\Support\Facades\DB::table('companies')->update([
            'modules' => json_encode(['memo', 'procurement'])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('modules');
        });
    }
};
