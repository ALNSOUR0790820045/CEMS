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
        Schema::create('guarantee_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guarantee_id')->constrained()->cascadeOnDelete();
            $table->date('claim_date');
            $table->decimal('claimed_amount', 15, 2);
            $table->text('claim_reason');
            $table->enum('status', ['pending', 'paid', 'disputed', 'resolved'])->default('pending');
            $table->date('resolution_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantee_claims');
    }
};
