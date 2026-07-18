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
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->date('date');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('item_name');
            $table->text('specification')->nullable();
            $table->integer('quantity');
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->string('priority')->default('Normal'); // High, Medium, Normal, Low
            $table->text('purpose')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('vendor')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Returned, Completed, Cancelled
            $table->foreignId('workflow_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('current_step_id')->nullable()->constrained('workflow_steps')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_requests');
    }
};
