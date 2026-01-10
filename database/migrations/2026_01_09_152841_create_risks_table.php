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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('risk_number')->unique(); // RSK-YYYY-XXXX
            $table->foreignId('risk_register_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['technical', 'financial', 'schedule', 'safety', 'environmental', 'contractual', 'resource', 'external']);
            $table->text('source')->nullable();
            $table->text('trigger_events')->nullable();
            $table->json('affected_objectives')->nullable(); // cost, time, quality, safety, scope
            $table->date('identification_date');
            $table->foreignId('identified_by_id')->constrained('users');
            
            // Probability and Impact
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('probability_score'); // 1-5
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('impact_score'); // 1-5
            $table->integer('risk_score'); // probability_score × impact_score
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical']); // auto-calculated
            
            // Cost and Schedule Impact
            $table->decimal('cost_impact_min', 15, 2)->nullable();
            $table->decimal('cost_impact_max', 15, 2)->nullable();
            $table->decimal('cost_impact_expected', 15, 2)->nullable();
            $table->integer('schedule_impact_days')->nullable();
            
            // Response Strategy
            $table->enum('response_strategy', ['avoid', 'mitigate', 'transfer', 'accept'])->nullable();
            $table->text('response_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            
            // Residual Risk
            $table->integer('residual_probability')->nullable(); // 1-5
            $table->integer('residual_impact')->nullable(); // 1-5
            $table->integer('residual_score')->nullable(); // residual_probability × residual_impact
            
            // Ownership and Status
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->enum('status', ['identified', 'analyzing', 'responding', 'monitoring', 'closed', 'occurred'])->default('identified');
            $table->date('due_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('closure_reason')->nullable();
            $table->text('lessons_learned')->nullable();
            
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('status');
            $table->index('risk_level');
            $table->index(['project_id', 'status']);
            $table->index('identification_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
