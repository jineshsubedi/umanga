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
            $table->foreignId('workflow_id')->nullable()->after('status')->constrained('workflows')->onDelete('set null');
            $table->foreignId('current_step_id')->nullable()->after('workflow_id')->constrained('workflow_steps')->onDelete('set null');
        });

        Schema::table('meeting_memo_reviews', function (Blueprint $table) {
            $table->foreignId('workflow_step_id')->nullable()->after('reviewed_by')->constrained('workflow_steps')->onDelete('set null');
            $table->text('signature_data')->nullable()->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_memo_reviews', function (Blueprint $table) {
            $table->dropForeign(['workflow_step_id']);
            $table->dropColumn(['workflow_step_id', 'signature_data']);
        });

        Schema::table('meeting_memos', function (Blueprint $table) {
            $table->dropForeign(['workflow_id']);
            $table->dropForeign(['current_step_id']);
            $table->dropColumn(['workflow_id', 'current_step_id']);
        });
    }
};
