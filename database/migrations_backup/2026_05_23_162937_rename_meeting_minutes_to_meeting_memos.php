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
        Schema::rename('meeting_minutes', 'meeting_memos');
        Schema::rename('meeting_minute_reviews', 'meeting_memo_reviews');
        Schema::rename('meeting_minute_attachments', 'meeting_memo_attachments');
        Schema::rename('meeting_minute_managers', 'meeting_memo_managers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('meeting_memos', 'meeting_minutes');
        Schema::rename('meeting_memo_reviews', 'meeting_minute_reviews');
        Schema::rename('meeting_memo_attachments', 'meeting_minute_attachments');
        Schema::rename('meeting_memo_managers', 'meeting_minute_managers');
    }
};
