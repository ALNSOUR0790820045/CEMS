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
        Schema::create('reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['outstanding_check', 'deposit_in_transit', 'bank_charge', 'bank_interest', 'error', 'other'])->default('other');
            $table->string('description');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->boolean('is_cleared')->default(false);
            $table->date('cleared_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bank_reconciliation_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_items');
    }
};
