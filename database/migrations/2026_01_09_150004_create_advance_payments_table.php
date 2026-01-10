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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->string('advance_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->enum('advance_type', ['mobilization', 'materials', 'equipment']);
            $table->decimal('advance_percentage', 5, 2);
            $table->decimal('advance_amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date')->nullable();
            $table->boolean('guarantee_required')->default(true);
            $table->foreignId('guarantee_id')->nullable()->constrained('retention_guarantees')->nullOnDelete();
            $table->decimal('recovery_start_percentage', 5, 2)->default(0);
            $table->decimal('recovery_percentage', 5, 2);
            $table->decimal('recovered_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'recovering', 'fully_recovered'])->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
