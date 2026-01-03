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
        Schema::create('vendor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            
            // Evaluation Period
            $table->date('evaluation_date');
            $table->date('evaluation_period_from')->nullable();
            $table->date('evaluation_period_to')->nullable();
            
            // Scores (1-5)
            $table->integer('quality_score');
            $table->integer('delivery_score');
            $table->integer('price_score');
            $table->integer('service_score');
            $table->integer('compliance_score');
            
            // Overall Score (computed average)
            $table->decimal('overall_score', 3, 2)->nullable();
            
            // Comments
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('recommendations')->nullable();
            
            // Evaluated By
            $table->unsignedBigInteger('evaluated_by_id');
            
            // Company (Multi-tenancy)
            $table->unsignedBigInteger('company_id');
            
            $table->timestamps();
            
            // Indexes
            $table->index('vendor_id');
            $table->index('evaluation_date');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_evaluations');
    }
};
