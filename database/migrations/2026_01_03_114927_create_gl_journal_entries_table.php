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
        Schema::create('gl_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number')->unique();
            
            $table->date('entry_date');
            $table->date('posting_date')->nullable();
            
            $table->enum('journal_type', [
                'general', 'opening_balance', 'closing', 
                'adjustment', 'reversal', 'recurring'
            ])->default('general');
            $table->enum('reference_type', [
                'manual', 'invoice', 'payment', 'purchase_order', 
                'ipc', 'payroll', 'other'
            ])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            
            $table->text('description');
            
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);
            
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            
            $table->enum('status', [
                'draft', 'pending_approval', 'approved', 
                'posted', 'cancelled', 'reversed'
            ])->default('draft');
            
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            
            $table->foreignId('reversed_from_id')->nullable()->constrained('gl_journal_entries')->nullOnDelete();
            $table->foreignId('reversed_by_id')->nullable()->constrained('gl_journal_entries')->nullOnDelete();
            
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            
            $table->string('attachment')->nullable();
            
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'entry_date']);
            $table->index(['journal_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_journal_entries');
    }
};
