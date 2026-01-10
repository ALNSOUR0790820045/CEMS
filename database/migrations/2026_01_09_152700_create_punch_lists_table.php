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
        Schema::create('punch_lists', function (Blueprint $table) {
            $table->id();
            $table->string('list_number')->unique(); // PL-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Classification
            $table->enum('list_type', ['pre_handover', 'handover', 'defects_liability', 'final'])->default('pre_handover');
            $table->string('area_zone')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->enum('discipline', ['architectural', 'structural', 'mep', 'civil', 'landscape'])->nullable();
            
            // Responsible Parties
            $table->foreignId('contractor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('subcontractor_id')->nullable()->constrained('vendors')->nullOnDelete();
            
            // Inspection Details
            $table->date('inspection_date')->nullable();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('consultant_rep')->nullable();
            $table->string('contractor_rep')->nullable();
            
            // Statistics
            $table->integer('total_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->integer('pending_items')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            
            // Dates
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            
            // Workflow Status
            $table->enum('status', ['draft', 'issued', 'in_progress', 'completed', 'verified', 'closed'])->default('draft');
            
            // Workflow Tracking
            $table->foreignId('issued_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('closed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_lists');
    }
};
