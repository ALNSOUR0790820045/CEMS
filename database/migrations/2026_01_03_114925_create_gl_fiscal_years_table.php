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
        Schema::create('gl_fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('year_name');
            $table->date('start_date');
            $table->date('end_date');
            
            $table->enum('status', ['open', 'closed', 'locked'])->default('open');
            
            $table->boolean('is_current')->default(false);
            
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['company_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_fiscal_years');
    }
};
