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
        Schema::create('diary_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable();
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->decimal('quantity_today', 15, 2)->default(0.00);
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('cumulative_quantity', 15, 2)->default(0.00);
            $table->decimal('percentage_complete', 5, 2)->default(0.00);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'delayed', 'on_hold'])->default('in_progress');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_activities');
    }
};
