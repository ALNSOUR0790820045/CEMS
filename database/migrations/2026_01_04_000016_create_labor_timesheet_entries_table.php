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
        // labor_timesheet_entries table
        Schema::create('labor_timesheet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timesheet_id')->constrained('labor_timesheets')->cascadeOnDelete();
            $table->foreignId('laborer_id')->constrained();
            $table->json('daily_hours'); // {"2026-01-01": {"regular": 8, "overtime": 2}, ...}
            $table->decimal('total_regular_hours', 10, 2)->default(0);
            $table->decimal('total_overtime_hours', 10, 2)->default(0);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('overtime_rate', 10, 2);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_timesheet_entries');
    }
};
