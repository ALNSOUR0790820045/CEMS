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
        Schema::create('financial_report_configs', function (Blueprint $table) {
            $table->id();
            $table->string('report_name')->unique();
            $table->enum('report_type', [
                'trial_balance', 
                'balance_sheet', 
                'income_statement', 
                'cash_flow',
                'general_ledger',
                'account_transactions',
                'ap_aging',
                'ar_aging',
                'vendor_statement',
                'customer_statement',
                'project_profitability',
                'cost_center',
                'budget_vs_actual',
                'payment_analysis',
                'tax_report'
            ]);
            $table->json('config_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_report_configs');
    }
};
