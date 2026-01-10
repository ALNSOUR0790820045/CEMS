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
        Schema::create('project_wbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('project_wbs')->cascadeOnDelete();
            $table->string('wbs_code'); // 1.1.1
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->integer('level')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['project_id', 'wbs_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_wbs');
    }
};
