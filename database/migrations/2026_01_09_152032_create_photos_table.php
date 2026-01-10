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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('photo_number')->unique(); // PHT-YYYY-XXXX
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('album_id')->nullable()->constrained('photo_albums')->nullOnDelete();
            
            // File information
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('medium_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // Content
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('category', ['progress', 'quality', 'safety', 'defect', 'before', 'after', 'milestone', 'general'])->default('general');
            $table->string('location')->nullable();
            $table->string('work_area')->nullable();
            $table->foreignId('activity_id')->nullable()->constrained('project_activities')->nullOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->nullOnDelete();
            
            // Date and time
            $table->date('taken_date')->nullable();
            $table->time('taken_time')->nullable();
            
            // GPS data
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            $table->decimal('gps_accuracy', 8, 2)->nullable();
            $table->string('gps_address')->nullable();
            
            // Camera metadata
            $table->string('camera_make')->nullable();
            $table->string('camera_model')->nullable();
            $table->integer('orientation')->nullable();
            $table->string('weather_condition')->nullable();
            
            // Tags and features
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_private')->default(false);
            
            // User tracking
            $table->foreignId('uploaded_by_id')->constrained('users');
            $table->foreignId('taken_by_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Approval
            $table->boolean('approved')->default(false);
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
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
        Schema::dropIfExists('photos');
    }
};
