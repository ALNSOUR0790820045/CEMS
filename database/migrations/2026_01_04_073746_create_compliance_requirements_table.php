<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('requirement_code')->unique();
            $table->string('requirement_name');
            $table->string('regulatory_body');
            $table->enum('requirement_type', ['license', 'permit', 'certification', 'audit', 'reporting']);
            $table->enum('applicable_to', ['company', 'project', 'department', 'employee']);
            $table->text('description');
            $table->enum('frequency', ['one_time', 'annual', 'quarterly', 'monthly']);
            $table->boolean('is_mandatory')->default(true);
            $table->text('penalty_description')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_requirements');
    }
};
