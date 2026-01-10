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
        Schema::create('defects_liability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('retention_id')->nullable()->constrained()->nullOnDelete();
            $table->date('taking_over_date');
            $table->date('dlp_start_date');
            $table->date('dlp_end_date');
            $table->integer('dlp_months');
            $table->date('final_certificate_date')->nullable();
            $table->enum('status', ['active', 'completed', 'extended'])->default('active');
            $table->integer('extension_months')->default(0);
            $table->text('extension_reason')->nullable();
            $table->integer('defects_reported')->default(0);
            $table->integer('defects_rectified')->default(0);
            $table->boolean('performance_bond_released')->default(false);
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
        Schema::dropIfExists('defects_liability');
    }
};
