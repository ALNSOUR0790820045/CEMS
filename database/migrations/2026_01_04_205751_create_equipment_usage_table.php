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
        Schema::create('equipment_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('equipment_assignments')->nullOnDelete();
            $table->date('usage_date');
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->decimal('start_meter', 10, 2)->nullable();
            $table->decimal('end_meter', 10, 2)->nullable();
            $table->decimal('fuel_consumed', 10, 2)->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('work_description')->nullable();
            $table->enum('condition', ['good', 'fair', 'needs_attention'])->default('good');
            $table->text('issues')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_usage');
    }
};
