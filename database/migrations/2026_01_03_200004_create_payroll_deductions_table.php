<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_entry_id')->constrained('payroll_entries')->cascadeOnDelete();
            $table->enum('deduction_type', ['tax', 'social_insurance', 'loan', 'advance', 'penalty', 'other'])->default('other');
            $table->string('deduction_name');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            
            $table->index('payroll_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
