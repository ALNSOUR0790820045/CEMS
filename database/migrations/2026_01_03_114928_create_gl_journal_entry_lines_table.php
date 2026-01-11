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
        Schema::create('gl_journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('gl_journal_entries')->cascadeOnDelete();
            $table->integer('line_number');
            
            $table->foreignId('gl_account_id')->constrained('gl_accounts');
            
            $table->decimal('debit_amount', 18, 2)->default(0);
            $table->decimal('credit_amount', 18, 2)->default(0);
            
            $table->text('description')->nullable();
            
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('base_currency_debit', 18, 2)->default(0);
            $table->decimal('base_currency_credit', 18, 2)->default(0);
            
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['journal_entry_id', 'line_number']);
            $table->index(['gl_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_journal_entry_lines');
    }
};
