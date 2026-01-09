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
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('reconciliation_number')->unique();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('reconciliation_date');
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('book_balance', 15, 2)->default(0);
            $table->decimal('bank_balance', 15, 2)->default(0);
            $table->decimal('adjusted_book_balance', 15, 2)->default(0);
            $table->decimal('adjusted_bank_balance', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved'])->default('draft');
            $table->foreignId('prepared_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
