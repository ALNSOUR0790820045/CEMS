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
        Schema::create('progress_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique(); // PB-YYYY-XXX
            $table->foreignId('project_id')->constrained()->onDelete('restrict');
            $table->foreignId('contract_id')->constrained()->onDelete('restrict');
            $table->date('period_from');
            $table->date('period_to');
            $table->date('bill_date');
            $table->enum('bill_type', ['interim', 'final', 'retention_release'])->default('interim');
            $table->integer('bill_sequence'); // 1, 2, 3...
            $table->foreignId('previous_bill_id')->nullable()->constrained('progress_bills')->onDelete('set null');
            
            // Amounts
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('previous_amount', 18, 2)->default(0);
            $table->decimal('current_amount', 18, 2)->default(0);
            
            // Retention
            $table->decimal('retention_percentage', 5, 2)->default(0);
            $table->decimal('retention_amount', 18, 2)->default(0);
            $table->decimal('cumulative_retention', 18, 2)->default(0);
            
            // Advance Recovery
            $table->decimal('advance_recovery_percentage', 5, 2)->default(0);
            $table->decimal('advance_recovery_amount', 18, 2)->default(0);
            
            // Other Deductions
            $table->decimal('other_deductions', 18, 2)->default(0);
            $table->text('deduction_remarks')->nullable();
            
            // Net Amount
            $table->decimal('net_amount', 18, 2)->default(0);
            
            // VAT
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->decimal('vat_amount', 18, 2)->default(0);
            
            // Total
            $table->decimal('total_payable', 18, 2)->default(0);
            
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');
            
            // Status and Workflow
            $table->enum('status', [
                'draft',
                'submitted',
                'reviewed',
                'certified',
                'approved',
                'paid',
                'rejected'
            ])->default('draft');
            
            // User tracking
            $table->foreignId('prepared_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('certified_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('certified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Payment
            $table->string('payment_reference')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['project_id', 'bill_sequence']);
            $table->index(['status']);
            $table->index(['bill_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bills');
    }
};
