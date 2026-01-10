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
        Schema::create('eot_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('time_bar_claim_id')->nullable()->constrained();
            $table->string('eot_number')->unique();
            
            // التواريخ
            $table->date('claim_date');
            $table->date('event_start_date');
            $table->date('event_end_date')->nullable();
            $table->integer('event_duration_days');
            
            // التمديد المطلوب
            $table->integer('requested_days');
            $table->date('requested_new_completion_date');
            
            // القرار
            $table->integer('approved_days')->nullable();
            $table->date('approved_new_completion_date')->nullable();
            $table->integer('rejected_days')->nullable();
            
            // السبب (FIDIC)
            $table->enum('cause_category', [
                'client_delay',
                'consultant_delay',
                'variations',
                'unforeseeable_conditions',
                'force_majeure',
                'weather',
                'delays_by_others',
                'suspension',
                'late_drawings',
                'late_approvals',
                'other'
            ]);
            
            // التفاصيل
            $table->text('event_description');
            $table->text('impact_description');
            $table->text('justification');
            $table->string('fidic_clause_reference')->nullable();
            
            // التكاليف الإطالة (Prolongation Costs)
            $table->boolean('has_prolongation_costs')->default(false);
            $table->decimal('site_overheads', 15, 2)->default(0);
            $table->decimal('head_office_overheads', 15, 2)->default(0);
            $table->decimal('equipment_costs', 15, 2)->default(0);
            $table->decimal('financing_costs', 15, 2)->default(0);
            $table->decimal('other_costs', 15, 2)->default(0);
            $table->decimal('total_prolongation_cost', 15, 2)->default(0);
            
            // الحالة
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review_consultant',
                'under_review_client',
                'partially_approved',
                'approved',
                'rejected',
                'disputed'
            ])->default('draft');
            
            // سلسلة الموافقات
            $table->foreignId('prepared_by')->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('consultant_reviewed_by')->nullable()->constrained('users');
            $table->timestamp('consultant_reviewed_at')->nullable();
            $table->text('consultant_comments')->nullable();
            
            $table->foreignId('client_approved_by')->nullable()->constrained('users');
            $table->timestamp('client_approved_at')->nullable();
            $table->text('client_comments')->nullable();
            
            // المستندات
            $table->json('supporting_documents')->nullable();
            
            // الأثر على الجدول الزمني
            $table->date('original_completion_date');
            $table->date('current_completion_date');
            $table->boolean('affects_critical_path')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eot_claims');
    }
};
