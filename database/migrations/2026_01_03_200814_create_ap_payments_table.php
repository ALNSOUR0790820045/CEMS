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
        Schema::create('ap_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit_card']);
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('check_number')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'cleared', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('gl_journal_entry_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ap_payments');
    }
};
