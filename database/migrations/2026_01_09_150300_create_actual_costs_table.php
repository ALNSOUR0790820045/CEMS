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
        Schema::create('actual_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cost_code_id')->constrained()->restrictOnDelete();
            $table->foreignId('budget_item_id')->nullable()->constrained('project_budget_items')->nullOnDelete();
            $table->date('transaction_date');
            $table->enum('reference_type', ['invoice', 'payroll', 'petty_cash', 'journal']);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_number');
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('unit_rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->decimal('amount_local', 15, 2);
            $table->foreignId('posted_by_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('posted_at');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'transaction_date']);
            $table->index(['cost_code_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_costs');
    }
};
