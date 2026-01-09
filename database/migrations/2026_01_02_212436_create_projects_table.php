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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Project details
            $table->date('start_date');
            $table->date('planned_end_date');
            $table->date('actual_end_date')->nullable();
            
            // Budget
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('contingency_budget', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning');
            $table->decimal('overall_progress', 5, 2)->default(0);
            
            // Location
            $table->string('location')->nullable();
            $table->string('client_name')->nullable();
            
            // Management
            $table->foreignId('project_manager_id')->nullable()->constrained('users');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
