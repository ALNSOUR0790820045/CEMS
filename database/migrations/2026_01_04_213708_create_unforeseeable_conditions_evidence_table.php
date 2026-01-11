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
        Schema::create('unforeseeable_conditions_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condition_id')->constrained('unforeseeable_conditions')->cascadeOnDelete();
            $table->enum('evidence_type', [
                'photo',
                'video',
                'soil_test',
                'survey_report',
                'expert_report',
                'witness_statement',
                'correspondence',
                'other'
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->date('evidence_date');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('capture_timestamp')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unforeseeable_conditions_evidence');
    }
};
