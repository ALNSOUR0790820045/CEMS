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
        Schema::create('punch_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            
            // Counts
            $table->integer('total_items')->default(0);
            $table->integer('open_items')->default(0);
            $table->integer('in_progress_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->integer('verified_items')->default(0);
            $table->integer('overdue_items')->default(0);
            
            // Breakdown
            $table->json('by_discipline')->nullable(); // {architectural: 10, structural: 5, ...}
            $table->json('by_severity')->nullable(); // {minor: 20, major: 8, critical: 2}
            $table->json('by_contractor')->nullable(); // {contractor_id: count}
            
            // Performance
            $table->decimal('avg_resolution_days', 8, 2)->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['project_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_statistics');
    }
};
