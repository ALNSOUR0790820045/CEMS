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
        Schema::create('daily_cash_positions', function (Blueprint $table) {
            $table->id();
            $table->date('position_date');
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('cascade');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('total_receipts', 15, 2)->default(0);
            $table->decimal('total_payments', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2);
            $table->boolean('is_reconciled')->default(false);
            $table->foreignId('reconciled_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reconciled_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['position_date', 'cash_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cash_positions');
    }
};
