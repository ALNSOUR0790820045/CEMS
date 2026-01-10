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
        Schema::create('project_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('issue_number');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['delay', 'quality', 'safety', 'design', 'commercial', 'other'])->default('other');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->date('identified_date');
            $table->date('target_resolution_date')->nullable();
            $table->date('actual_resolution_date')->nullable();
            $table->text('resolution')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_issues');
    }
};
