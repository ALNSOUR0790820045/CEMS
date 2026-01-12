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
        Schema::create('employee_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            
            $table->enum('qualification_type', ['degree', 'diploma', 'certificate', 'training', 'license']);
            $table->string('qualification_name');
            
            $table->string('institution')->nullable();
            $table->string('specialization')->nullable();
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->string('grade')->nullable();
            
            $table->string('attachment_path')->nullable();
            
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_qualifications');
    }
};
