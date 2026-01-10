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
        Schema::create('progress_bill_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->enum('deduction_type', [
                'retention',
                'advance_recovery',
                'penalty',
                'defects',
                'materials',
                'other'
            ]);
            $table->text('description');
            $table->enum('calculation_basis', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('base_amount', 18, 2)->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['progress_bill_id']);
            $table->index(['deduction_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bill_deductions');
    }
};
