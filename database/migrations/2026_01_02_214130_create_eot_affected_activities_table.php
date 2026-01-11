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
        Schema::create('eot_affected_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eot_claim_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('project_activities');
            
            $table->date('original_end_date');
            $table->date('revised_end_date');
            $table->integer('delay_days');
            $table->boolean('on_critical_path')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eot_affected_activities');
    }
};
