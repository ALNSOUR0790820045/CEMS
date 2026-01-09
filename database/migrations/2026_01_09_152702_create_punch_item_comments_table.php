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
        Schema::create('punch_item_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punch_item_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->foreignId('commented_by_id')->constrained('users')->cascadeOnDelete();
            $table->enum('comment_type', ['note', 'query', 'response', 'rejection'])->default('note');
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('punch_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_item_comments');
    }
};
