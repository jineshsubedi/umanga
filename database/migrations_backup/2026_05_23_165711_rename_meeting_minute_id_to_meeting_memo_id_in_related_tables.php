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
        Schema::table('meeting_memo_reviews', function (Blueprint $table) {
            $table->renameColumn('meeting_minute_id', 'meeting_memo_id');
        });
        Schema::table('meeting_memo_attachments', function (Blueprint $table) {
            $table->renameColumn('meeting_minute_id', 'meeting_memo_id');
        });
        Schema::table('meeting_memo_managers', function (Blueprint $table) {
            $table->renameColumn('meeting_minute_id', 'meeting_memo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_memo_reviews', function (Blueprint $table) {
            $table->renameColumn('meeting_memo_id', 'meeting_minute_id');
        });
        Schema::table('meeting_memo_attachments', function (Blueprint $table) {
            $table->renameColumn('meeting_memo_id', 'meeting_minute_id');
        });
        Schema::table('meeting_memo_managers', function (Blueprint $table) {
            $table->renameColumn('meeting_memo_id', 'meeting_minute_id');
        });
    }
};
