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
        // labor_assignments table (تخصيص العمالة للمشاريع)
        Schema::create('labor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laborer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained();
            $table->date('assignment_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('work_scope')->nullable();
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_assignments');
    }
};
