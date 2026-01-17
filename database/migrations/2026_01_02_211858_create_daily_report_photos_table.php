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
        Schema::create('daily_report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('photo_title')->nullable();
            $table->text('description')->nullable();
            
            // GPS Metadata (غير قابل للتعديل)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('captured_at'); // الوقت الدقيق للتقاط الصورة
            $table->string('device_info')->nullable();
            
            // تصنيف
            $table->enum('category', ['progress', 'problem', 'safety', 'quality', 'material', 'equipment', 'general'])->default('progress');
            $table->foreignId('activity_id')->nullable()->constrained('project_activities');
            $table->string('location_name')->nullable(); // "الطابق الأول - الجدار الشرقي"
            
            // Blockchain Hash (للإثبات القانوني)
            $table->string('hash')->unique(); // SHA-256 hash
            $table->boolean('verified')->default(false);
            
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_photos');
    }
};
