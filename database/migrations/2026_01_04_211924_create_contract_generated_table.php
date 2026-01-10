<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_generated', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('contract_templates');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('tender_id')->nullable();
            
            $table->string('contract_title');
            $table->json('parties'); // أطراف العقد
            $table->json('filled_data'); // البيانات المعبأة
            $table->json('modified_clauses')->nullable(); // البنود المعدلة
            $table->json('added_special_conditions')->nullable();
            
            $table->enum('status', ['draft', 'review', 'approved', 'signed'])->default('draft');
            $table->string('generated_file')->nullable();
            
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_generated');
    }
};
