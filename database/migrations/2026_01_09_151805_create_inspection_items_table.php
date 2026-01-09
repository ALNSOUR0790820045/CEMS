<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('checklist_item_id')->nullable();
            $table->text('item_description');
            $table->text('acceptance_criteria')->nullable();
            $table->enum('result', ['pass', 'fail', 'na'])->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('actual_value')->nullable();
            $table->text('remarks')->nullable();
            $table->json('photo_ids')->nullable();
            $table->boolean('requires_action')->default(false);
            $table->timestamps();
            
            $table->index('inspection_id');
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
