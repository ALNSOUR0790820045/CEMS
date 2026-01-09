<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_number')->unique();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->foreignId('shift_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('salary', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_number');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
