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
        Schema::create('cost_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('cost_codes')->nullOnDelete();
            $table->integer('level')->default(1);
            $table->enum('cost_type', ['direct', 'indirect'])->default('direct');
            $table->enum('cost_category', ['material', 'labor', 'equipment', 'subcontractor', 'overhead'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->index(['parent_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_codes');
    }
};
