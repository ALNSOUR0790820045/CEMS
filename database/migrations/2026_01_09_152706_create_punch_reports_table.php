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
        Schema::create('punch_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // PLR-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('report_type', ['summary', 'detailed', 'location', 'discipline', 'contractor'])->default('summary');
            $table->date('report_date');
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->json('filters')->nullable(); // Store filter criteria
            $table->string('generated_path')->nullable(); // Path to generated PDF
            $table->foreignId('generated_by_id')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_reports');
    }
};
