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
        Schema::create('photo_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // PR-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('report_type', ['weekly', 'monthly', 'milestone', 'handover'])->default('weekly');
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->text('cover_page_text')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('generated_path')->nullable();
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_reports');
    }
};
