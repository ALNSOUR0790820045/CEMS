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
        Schema::create('tender_wbs_boq_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_wbs_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_boq_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // Prevent duplicate mappings
            $table->unique(['tender_wbs_id', 'tender_boq_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_wbs_boq_mapping');
    }
};
