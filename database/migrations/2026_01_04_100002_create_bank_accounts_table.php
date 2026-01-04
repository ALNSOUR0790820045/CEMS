<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('branch')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('book_balance', 15, 2)->default(0);
            $table->foreignId('gl_account_id')->constrained()->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
