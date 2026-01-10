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
        Schema::create('project_timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('project_activities');
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('work_date');
            
            // ساعات العمل
            $table->decimal('regular_hours', 5, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('total_hours', 5, 2)->default(0);
            
            // العمل المنجز
            $table->text('work_description')->nullable();
            $table->decimal('progress_achieved', 5, 2)->default(0); // نسبة الإنجاز المحققة اليوم
            
            // التكلفة
            $table->decimal('cost', 15, 2)->default(0);
            
            // الموافقات
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timesheets');
    }
};
