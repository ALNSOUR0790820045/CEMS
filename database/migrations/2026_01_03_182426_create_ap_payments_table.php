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
        Schema::create('ap_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('payment_amount', 15, 2);
            $table->foreignId('currency_id')->constrained();
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit_card']);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ap_payments');
    }
};
