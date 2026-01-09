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
        Schema::create('prolongation_cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eot_claim_id')->constrained()->cascadeOnDelete();
            
            $table->enum('cost_category', [
                'site_staff',
                'site_facilities',
                'equipment_rental',
                'utilities',
                'security',
                'insurance',
                'head_office',
                'financing',
                'other'
            ]);
            
            $table->text('description');
            $table->integer('duration_days');
            $table->decimal('daily_rate', 15, 2);
            $table->decimal('total_cost', 15, 2);
            
            $table->string('supporting_document')->nullable();
            $table->text('justification')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prolongation_cost_items');
    }
};
