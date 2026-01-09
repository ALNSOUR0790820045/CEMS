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
        Schema::create('vendor_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            
            // Bank Information
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            
            // Account Information
            $table->string('account_number');
            $table->string('account_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            
            // Currency
            $table->unsignedBigInteger('currency_id')->nullable();
            
            // Status
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Company (Multi-tenancy)
            $table->unsignedBigInteger('company_id');
            
            $table->timestamps();
            
            // Indexes
            $table->index('vendor_id');
            $table->index('company_id');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_bank_accounts');
    }
};
