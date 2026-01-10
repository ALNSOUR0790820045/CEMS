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
        Schema::create('punch_item_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punch_item_id')->constrained()->cascadeOnDelete();
            $table->enum('action', [
                'created', 
                'assigned', 
                'status_changed', 
                'comment_added', 
                'photo_added', 
                'verified', 
                'rejected'
            ]);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('performed_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('performed_at');
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['punch_item_id', 'performed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_item_history');
    }
};
