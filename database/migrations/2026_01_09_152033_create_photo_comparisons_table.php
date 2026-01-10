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
        Schema::create('photo_comparisons', function (Blueprint $table) {
            $table->id();
            $table->string('comparison_number')->unique(); // CMP-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('before_photo_id')->constrained('photos')->cascadeOnDelete();
            $table->foreignId('after_photo_id')->constrained('photos')->cascadeOnDelete();
            $table->date('comparison_date')->nullable();
            $table->string('location')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_comparisons');
    }
};
