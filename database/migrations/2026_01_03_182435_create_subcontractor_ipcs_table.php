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
        Schema::create('subcontractor_ipcs', function (Blueprint $table) {
            $table->id();
            $table->string('ipc_number')->unique();
            $table->date('ipc_date');
            $table->date('period_from');
            $table->date('period_to');
            
            $table->foreignId('subcontractor_agreement_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcontractor_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            $table->enum('ipc_type', ['interim', 'final'])->default('interim');
            
            $table->decimal('current_work_value', 15, 2);
            $table->decimal('previous_cumulative', 15, 2)->default(0);
            $table->decimal('materials_on_site', 15, 2)->default(0);
            
            $table->decimal('previous_advance_payment', 15, 2)->default(0);
            $table->decimal('current_advance_deduction', 15, 2)->default(0);
            
            $table->decimal('retention_percentage', 5, 2);
            
            $table->decimal('previous_back_charges', 15, 2)->default(0);
            $table->decimal('current_back_charges', 15, 2)->default(0);
            
            $table->foreignId('currency_id')->constrained();
            
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'paid', 'rejected'])->default('draft');
            
            $table->boolean('submitted_by_subcontractor')->default(false);
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->foreignId('payment_id')->nullable()->constrained('ap_payments')->onDelete('set null');
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_ipcs');
    }
};
