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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
