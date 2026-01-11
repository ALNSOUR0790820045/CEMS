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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Job details
            $table->string('job_title');
            $table->string('department')->nullable();
            $table->date('hire_date')->nullable();
            
            // Cost
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('overtime_rate', 10, 2)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('is_active');
            $table->index('hire_date');
            $table->index(['company_id', 'is_active']);
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
