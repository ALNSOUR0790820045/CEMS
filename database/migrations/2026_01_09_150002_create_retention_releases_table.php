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
        Schema::create('retention_releases', function (Blueprint $table) {
            $table->id();
            $table->string('release_number')->unique();
            $table->foreignId('retention_id')->constrained()->cascadeOnDelete();
            $table->enum('release_type', ['partial', 'first_moiety', 'second_moiety', 'full']);
            $table->date('release_date');
            $table->decimal('release_amount', 15, 2);
            $table->decimal('release_percentage', 5, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->string('release_condition_met')->nullable();
            $table->date('condition_date')->nullable();
            $table->string('certificate_reference')->nullable();
            $table->boolean('bank_guarantee_returned')->default(false);
            $table->date('guarantee_return_date')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'released', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_releases');
    }
};
