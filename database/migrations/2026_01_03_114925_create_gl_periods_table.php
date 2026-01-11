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
        Schema::create('gl_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('gl_fiscal_years')->cascadeOnDelete();
            $table->integer('period_number');
            $table->string('period_name');
            
            $table->date('start_date');
            $table->date('end_date');
            
            $table->enum('status', ['open', 'closed', 'locked'])->default('open');
            
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['fiscal_year_id', 'period_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_periods');
    }
};
