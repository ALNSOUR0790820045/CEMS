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
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->enum('transaction_type', ['receipt', 'payment', 'transfer_in', 'transfer_out', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('counterparty_type')->nullable();
            $table->unsignedBigInteger('counterparty_id')->nullable();
            $table->string('counterparty_name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->foreignId('posted_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('gl_journal_entry_id')->nullable()->constrained('gl_journal_entries')->onDelete('set null');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['reference_type', 'reference_id']);
            $table->index(['counterparty_type', 'counterparty_id']);
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
