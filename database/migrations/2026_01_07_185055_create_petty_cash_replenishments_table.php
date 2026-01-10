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
        Schema::create('petty_cash_replenishments', function (Blueprint $table) {
            $table->id();
            $table->string('replenishment_number')->unique();
            $table->date('replenishment_date');
            $table->foreignId('petty_cash_account_id')->constrained('petty_cash_accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'check', 'transfer']);
            $table->string('reference_number')->nullable();
            $table->string('from_account_type')->nullable(); // 'cash' or 'bank'
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->foreignId('requested_by_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('petty_cash_replenishments');
    }
};
