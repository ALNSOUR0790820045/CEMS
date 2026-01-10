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
        Schema::create('advance_recoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_payment_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('progress_bill_id')->nullable();
            $table->date('bill_date');
            $table->decimal('bill_amount', 15, 2);
            $table->decimal('recovery_percentage', 5, 2);
            $table->decimal('recovery_amount', 15, 2);
            $table->decimal('cumulative_recovery', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_recoveries');
    }
};
