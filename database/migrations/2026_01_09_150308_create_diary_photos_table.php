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
        Schema::create('diary_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('caption')->nullable();
            $table->string('location')->nullable();
            $table->enum('category', ['progress', 'quality', 'safety', 'general'])->default('general');
            $table->foreignId('taken_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('taken_at')->nullable();
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_photos');
    }
};
