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
        Schema::create('main_ipcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('ipc_number')->unique(); // IPC-001
            $table->integer('ipc_sequence'); // 1, 2, 3...
            
            // الفترة
            $table->date('period_from');
            $table->date('period_to');
            $table->date('submission_date');
            
            // القيم المالية
            $table->decimal('previous_cumulative', 15, 2)->default(0);
            $table->decimal('current_period_work', 15, 2)->default(0);
            $table->decimal('current_cumulative', 15, 2)->default(0);
            
            // Change Orders
            $table->decimal('approved_change_orders', 15, 2)->default(0);
            
            // الخصومات
            $table->decimal('retention_percent', 5, 2)->nullable();
            $table->decimal('retention_amount', 15, 2)->default(0);
            $table->decimal('advance_payment_deduction', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->text('deductions_notes')->nullable();
            
            // الضرائب
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            
            // المبلغ الصافي
            $table->decimal('net_payable', 15, 2)->default(0);
            
            // الحالة
            $table->enum('status', [
                'draft',
                'pending_pm',           // 1. مدير المشروع
                'pending_technical',    // 2. المدير الفني
                'pending_consultant',   // 3. الاستشاري (7-14 يوم)
                'pending_client',       // 4. العميل (14-21 يوم)
                'pending_finance',      // 5. المالية (الشركة)
                'approved_for_payment', // 6. جاهز للدفع
                'paid',
                'rejected',
                'on_hold'
            ])->default('draft');
            
            // سلسلة الموافقات (6 مراحل)
            
            // 1. مدير المشروع (إعداد)
            $table->foreignId('pm_prepared_by')->nullable()->constrained('users');
            $table->timestamp('pm_prepared_at')->nullable();
            $table->text('pm_notes')->nullable();
            
            // 2. المدير الفني (مراجعة)
            $table->foreignId('technical_reviewed_by')->nullable()->constrained('users');
            $table->timestamp('technical_reviewed_at')->nullable();
            $table->enum('technical_decision', ['pending', 'approved', 'rejected', 'revision_required'])->default('pending');
            $table->text('technical_comments')->nullable();
            
            // 3. الاستشاري (مراجعة 7-14 يوم)
            $table->foreignId('consultant_reviewed_by')->nullable()->constrained('users');
            $table->date('consultant_submission_date')->nullable();
            $table->date('consultant_due_date')->nullable(); // +14 days
            $table->timestamp('consultant_reviewed_at')->nullable();
            $table->enum('consultant_decision', ['pending', 'approved', 'rejected', 'revision_required'])->default('pending');
            $table->decimal('consultant_approved_amount', 15, 2)->nullable();
            $table->text('consultant_comments')->nullable();
            $table->integer('consultant_review_days')->nullable();
            
            // 4. العميل (اعتماد 14-21 يوم)
            $table->foreignId('client_approved_by')->nullable()->constrained('users');
            $table->date('client_submission_date')->nullable();
            $table->date('client_due_date')->nullable(); // +21 days
            $table->timestamp('client_approved_at')->nullable();
            $table->enum('client_decision', ['pending', 'approved', 'rejected', 'revision_required'])->default('pending');
            $table->decimal('client_approved_amount', 15, 2)->nullable();
            $table->text('client_comments')->nullable();
            $table->integer('client_review_days')->nullable();
            
            // 5. المراجعة المالية (الشركة)
            $table->foreignId('finance_reviewed_by')->nullable()->constrained('users');
            $table->timestamp('finance_reviewed_at')->nullable();
            $table->enum('finance_decision', ['pending', 'approved', 'on_hold'])->default('pending');
            $table->text('finance_comments')->nullable();
            
            // 6. الدفع
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->date('payment_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            
            // المرفقات
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            
            $table->unique(['project_id', 'ipc_sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_ipcs');
    }
};
