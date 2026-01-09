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
        Schema::create('subcontractor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontractor_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained();
            
            $table->date('evaluation_date');
            $table->date('evaluation_period_from')->nullable();
            $table->date('evaluation_period_to')->nullable();
            
            $table->integer('quality_score'); // 1-5
            $table->integer('time_performance_score'); // 1-5
            $table->integer('safety_score'); // 1-5
            $table->integer('cooperation_score'); // 1-5
            
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('recommendations')->nullable();
            
            $table->foreignId('evaluated_by_id')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_evaluations');
    }
};
