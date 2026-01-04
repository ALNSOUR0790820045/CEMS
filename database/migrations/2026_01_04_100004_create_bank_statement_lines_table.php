<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->date('value_date')->nullable();
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->string('matched_transaction_type')->nullable();
            $table->bigInteger('matched_transaction_id')->nullable();
            $table->timestamps();
            
            $table->index(['is_reconciled', 'bank_statement_id']);
            $table->index(['matched_transaction_type', 'matched_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
    }
};
