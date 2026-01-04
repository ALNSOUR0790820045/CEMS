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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['receipt', 'payment', 'transfer']);
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit_card']);
            $table->string('reference_number')->nullable();
            $table->string('payee_payer')->nullable();
            $table->text('description');
            $table->string('related_document_type')->nullable();
            $table->unsignedBigInteger('related_document_id')->nullable();
            $table->foreignId('gl_journal_entry_id')->nullable()->constrained('gl_journal_entries')->onDelete('set null');
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
