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
        Schema::create('gl_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->string('account_name_en')->nullable();
            
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('account_category', [
                'current_asset', 'fixed_asset', 'current_liability', 
                'long_term_liability', 'equity', 'operating_revenue', 
                'other_revenue', 'operating_expense', 'other_expense'
            ])->nullable();
            
            $table->foreignId('parent_account_id')->nullable()->constrained('gl_accounts')->nullOnDelete();
            $table->integer('account_level')->default(1);
            
            $table->boolean('is_main_account')->default(false);
            $table->boolean('is_control_account')->default(false);
            $table->boolean('allow_posting')->default(true);
            
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->boolean('is_multi_currency')->default(false);
            
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('current_balance', 18, 2)->default(0);
            
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'account_type']);
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_accounts');
    }
};
