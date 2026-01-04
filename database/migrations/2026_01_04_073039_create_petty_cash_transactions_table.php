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
        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->foreignId('petty_cash_account_id')->constrained('petty_cash_accounts');
            $table->enum('transaction_type', ['expense', 'reimbursement', 'adjustment']);
            $table->decimal('amount', 10, 2);
            $table->string('expense_category')->nullable();
            $table->text('description');
            $table->string('payee')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->unsignedBigInteger('gl_journal_entry_id')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
