<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('caption')->nullable();
            $table->string('location')->nullable();
            $table->enum('category', ['before', 'during', 'after', 'defect', 'passed'])->default('during');
            $table->timestamp('taken_at')->nullable();
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            $table->foreignId('taken_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('inspection_id');
            $table->index('inspection_item_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_photos');
    }
};
