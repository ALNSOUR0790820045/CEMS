<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_entry_id')->constrained('payroll_entries')->cascadeOnDelete();
            $table->enum('allowance_type', ['housing', 'transport', 'food', 'mobile', 'other'])->default('other');
            $table->string('allowance_name');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_taxable')->default(true);
            $table->timestamps();
            
            $table->index('payroll_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_allowances');
    }
};
