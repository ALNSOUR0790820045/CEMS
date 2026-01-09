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
        // labor_daily_attendance table (الحضور اليومي)
        Schema::create('labor_daily_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laborer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained();
            $table->date('attendance_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('regular_hours', 4, 2)->default(0);
            $table->decimal('overtime_hours', 4, 2)->default(0);
            $table->decimal('total_hours', 4, 2)->default(0);
            $table->enum('status', ['present', 'absent', 'half_day', 'leave', 'sick'])->default('present');
            $table->text('work_area')->nullable();
            $table->text('work_description')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['laborer_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_daily_attendance');
    }
};
