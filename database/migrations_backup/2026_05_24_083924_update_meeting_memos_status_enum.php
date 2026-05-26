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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE meeting_memos MODIFY COLUMN status VARCHAR(255) DEFAULT 'draft'");
        \Illuminate\Support\Facades\DB::statement("UPDATE meeting_memos SET status = 'pending_manager' WHERE status = 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE meeting_memos SET status = 'pending' WHERE status IN ('pending_manager', 'pending_admin')");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE meeting_memos MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft'");
    }
};
