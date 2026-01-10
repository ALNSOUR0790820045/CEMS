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
        Schema::create('cost_plus_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('cost_plus_contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained();
            
            $table->date('transaction_date');
            $table->enum('cost_type', [
                'material',         // مواد
                'labor',            // عمالة
                'equipment',        // معدات
                'subcontract',      // مقاولي باطن
                'overhead',         // مصاريف غير مباشرة
                'other'
            ]);
            
            $table->string('description');
            $table->string('vendor_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            
            $table->decimal('gross_amount', 18, 2);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2);
            $table->string('currency', 3)->default('JOD');
            
            // التوثيق المطلوب (4 مستندات)
            $table->boolean('has_original_invoice')->default(false);
            $table->string('original_invoice_file')->nullable();
            $table->boolean('has_payment_receipt')->default(false);
            $table->string('payment_receipt_file')->nullable();
            $table->boolean('has_grn')->default(false);
            $table->foreignId('grn_id')->nullable()->constrained('goods_receipt_notes');
            $table->boolean('has_photo_evidence')->default(false);
            $table->string('photo_file')->nullable();
            $table->decimal('photo_latitude', 10, 8)->nullable();
            $table->decimal('photo_longitude', 11, 8)->nullable();
            $table->timestamp('photo_timestamp')->nullable();
            
            // الحالة
            $table->boolean('documentation_complete')->default(false);
            $table->boolean('is_reimbursable')->default(true);
            $table->enum('status', [
                'pending',          // بانتظار التوثيق
                'documented',       // موثق
                'approved',         // معتمد
                'rejected',         // مرفوض
                'invoiced',         // تم تضمينه في فاتورة
                'paid'              // مدفوع
            ])->default('pending');
            
            $table->text('rejection_reason')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_plus_transactions');
    }
};
