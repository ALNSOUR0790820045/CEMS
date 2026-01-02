<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contingency Reserve
        Schema::create('tender_contingency_reserves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('total_risk_exposure', 15, 2)->default(0);
            $table->decimal('contingency_percentage', 5, 2)->default(10.00);
            $table->decimal('contingency_amount', 15, 2)->default(0);
            
            $table->text('justification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_contingency_reserves');
    }
};
