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
        Schema::create('bill_approval_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->enum('approval_stage', ['prepared', 'reviewed', 'certified', 'approved']);
            $table->foreignId('approver_id')->constrained('users')->onDelete('restrict');
            $table->enum('action', ['approve', 'reject', 'return']);
            $table->text('comments')->nullable();
            $table->timestamp('actioned_at');
            $table->timestamps();
            
            // Indexes
            $table->index(['progress_bill_id']);
            $table->index(['approver_id']);
            $table->index(['approval_stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_approval_workflow');
    }
};
