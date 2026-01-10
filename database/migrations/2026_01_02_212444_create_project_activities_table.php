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
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('project_activities')->nullOnDelete();
            
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            
            // Schedule
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            
            // Duration
            $table->integer('planned_duration_days')->default(0);
            $table->integer('actual_duration_days')->default(0);
            
            // Budget & Cost
            $table->decimal('planned_budget', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            
            // Progress
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->decimal('weight', 5, 2)->default(0); // Weight in overall project
            
            // Status
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->boolean('is_critical')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_activities');
    }
};
