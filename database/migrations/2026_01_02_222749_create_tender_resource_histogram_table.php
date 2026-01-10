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
        Schema::create('tender_resource_histogram', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_resource_id')->constrained();
            $table->date('period_date');
            $table->decimal('required_units', 10, 2);
            $table->decimal('cost', 15, 2);
            $table->timestamps();
            
            $table->unique(['tender_resource_id', 'period_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_resource_histogram');
    }
};
