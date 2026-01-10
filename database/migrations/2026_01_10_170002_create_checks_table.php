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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('check_type', ['current', 'post_dated', 'deferred'])->default('current');
            $table->decimal('amount', 15, 3);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->decimal('amount_in_base_currency', 15, 3)->nullable();
            $table->text('amount_words')->nullable();
            $table->text('amount_words_en')->nullable();
            $table->string('beneficiary');
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('restrict');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->enum('status', ['issued', 'pending', 'due', 'cleared', 'bounced', 'cancelled'])->default('issued');
            $table->foreignId('template_id')->nullable()->constrained('payment_templates')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['bank_account_id', 'check_number']);
            $table->index('status');
            $table->index('due_date');
            $table->index('check_type');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
