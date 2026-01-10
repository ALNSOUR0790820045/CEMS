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
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->string('account_name_en')->nullable();
            $table->enum('account_type', ['cash', 'bank', 'petty_cash', 'safe']);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->foreignId('gl_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->foreignId('custodian_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};
