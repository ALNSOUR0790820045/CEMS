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
        Schema::create('promissory_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_number')->unique();
            $table->date('issue_date');
            $table->date('maturity_date');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 15, 3);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->decimal('amount_in_base_currency', 15, 3)->nullable();
            $table->text('amount_words')->nullable();
            $table->text('amount_words_en')->nullable();
            $table->string('issuer_name');
            $table->string('issuer_cr')->nullable();
            $table->text('issuer_address')->nullable();
            $table->string('payee_name');
            $table->text('payee_address')->nullable();
            $table->string('place_of_issue')->nullable();
            $table->text('purpose')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->enum('status', ['issued', 'pending', 'paid', 'dishonored', 'cancelled'])->default('issued');
            $table->foreignId('template_id')->nullable()->constrained('payment_templates')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('maturity_date');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promissory_notes');
    }
};
