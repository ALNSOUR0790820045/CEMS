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
        Schema::create('photo_albums', function (Blueprint $table) {
            $table->id();
            $table->string('album_number')->unique(); // ALB-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->enum('album_type', ['progress', 'quality', 'safety', 'milestone', 'handover', 'general'])->default('general');
            $table->foreignId('cover_photo_id')->nullable()->constrained('photos')->nullOnDelete();
            $table->integer('photos_count')->default(0);
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_albums');
    }
};
