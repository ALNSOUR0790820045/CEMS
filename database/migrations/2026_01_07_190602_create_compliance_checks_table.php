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
        Schema::create('compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_number')->unique();
            $table->foreignId('compliance_requirement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('check_date');
            $table->date('due_date');
            $table->enum('status', ['pending', 'passed', 'failed', 'waived'])->default('pending');
            $table->foreignId('checked_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('findings')->nullable();
            $table->text('corrective_action')->nullable();
            $table->string('evidence_path')->nullable();
            $table->date('next_check_date')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['due_date']);
            $table->index(['compliance_requirement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_checks');
    }
};
