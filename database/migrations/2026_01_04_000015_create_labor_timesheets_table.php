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
        // labor_timesheets table (الجداول الزمنية)
        Schema::create('labor_timesheets', function (Blueprint $table) {
            $table->id();
            $table->string('timesheet_number')->unique();
            $table->foreignId('project_id')->constrained();
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->decimal('total_regular_hours', 10, 2)->default(0);
            $table->decimal('total_overtime_hours', 10, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_timesheets');
    }
};
