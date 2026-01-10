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
        Schema::create('retention_guarantees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retention_id')->constrained()->cascadeOnDelete();
            $table->enum('guarantee_type', ['bank_guarantee', 'insurance_bond', 'cash']);
            $table->string('guarantee_number')->unique();
            $table->unsignedBigInteger('issuing_bank_id')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->boolean('in_lieu_of_retention')->default(false);
            $table->date('replacement_date')->nullable();
            $table->enum('status', ['active', 'expired', 'released', 'claimed'])->default('active');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_guarantees');
    }
};
