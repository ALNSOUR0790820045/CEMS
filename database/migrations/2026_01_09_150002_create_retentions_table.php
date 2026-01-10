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
        Schema::create('retentions', function (Blueprint $table) {
            $table->id();
            $table->string('retention_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->enum('retention_type', ['performance', 'defects_liability', 'advance_payment', 'materials']);
            $table->decimal('retention_percentage', 5, 2);
            $table->decimal('max_retention_percentage', 5, 2);
            $table->enum('release_schedule', ['single', 'staged']);
            $table->decimal('first_release_percentage', 5, 2)->nullable();
            $table->enum('first_release_condition', ['practical_completion', 'taking_over', 'final_certificate'])->nullable();
            $table->decimal('second_release_percentage', 5, 2)->nullable();
            $table->enum('second_release_condition', ['defects_liability_end', 'final_account'])->nullable();
            $table->integer('defects_liability_period_months')->nullable();
            $table->date('dlp_start_date')->nullable();
            $table->date('dlp_end_date')->nullable();
            $table->decimal('total_contract_value', 15, 2);
            $table->decimal('total_retention_amount', 15, 2)->default(0);
            $table->decimal('released_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['accumulating', 'held', 'partially_released', 'fully_released', 'forfeited'])->default('accumulating');
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
        Schema::dropIfExists('retentions');
    }
};
