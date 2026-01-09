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
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->date('transfer_date');
            $table->foreignId('from_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->foreignId('to_account_id')->constrained('cash_accounts')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->foreignId('from_currency_id')->constrained('currencies')->onDelete('restrict');
            $table->foreignId('to_currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('fees', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('requested_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transfers');
    }
};
