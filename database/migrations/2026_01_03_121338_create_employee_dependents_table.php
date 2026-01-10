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
        Schema::create('employee_dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            
            $table->string('full_name');
            $table->enum('relationship', ['spouse', 'son', 'daughter', 'father', 'mother', 'other']);
            
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            
            $table->boolean('is_covered_by_insurance')->default(false);
            
            $table->text('notes')->nullable();
            
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_dependents');
    }
};
