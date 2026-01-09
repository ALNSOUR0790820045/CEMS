<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('category', [
                'structural', 'mep', 'architectural', 'civil', 'safety', 'environmental'
            ]);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('default_checklist_id')->nullable();
            $table->boolean('requires_witness')->default(false);
            $table->boolean('requires_approval')->default(true);
            $table->enum('frequency', ['once', 'daily', 'weekly', 'milestone'])->default('once');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_types');
    }
};
