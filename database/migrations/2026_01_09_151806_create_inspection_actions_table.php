<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_item_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('action_type', ['corrective', 'preventive'])->default('corrective');
            $table->text('description');
            $table->foreignId('assigned_to_id')->constrained('users')->cascadeOnDelete();
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'verified'])->default('pending');
            $table->date('verification_date')->nullable();
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('inspection_id');
            $table->index('inspection_item_id');
            $table->index('assigned_to_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_actions');
    }
};
