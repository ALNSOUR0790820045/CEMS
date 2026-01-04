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
            $table->enum('account_type', ['cash', 'bank', 'petty_cash']);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->foreignId('gl_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
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
