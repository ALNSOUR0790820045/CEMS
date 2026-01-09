<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_number')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('inspection_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('version', 20)->default('1.0');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('inspection_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_templates');
    }
};
