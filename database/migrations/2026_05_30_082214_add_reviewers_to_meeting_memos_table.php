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
        Schema::table('meeting_memos', function (Blueprint $table) {
            $table->foreignId('checker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_memos', function (Blueprint $table) {
            $table->dropForeign(['checker_id']);
            $table->dropForeign(['verifier_id']);
            $table->dropForeign(['approver_id']);
            $table->dropColumn(['checker_id', 'verifier_id', 'approver_id']);
        });
    }
};
