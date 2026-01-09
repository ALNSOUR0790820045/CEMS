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
        Schema::create('site_receipt_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_receipt_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('title')->nullable();
            
            // GPS + Timestamp (غير قابل للتعديل)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('captured_at');
            $table->string('device_info')->nullable();
            
            // Hash للإثبات
            $table->string('hash')->unique();
            $table->boolean('verified')->default(true);
            
            $table->enum('category', ['vehicle', 'materials', 'documents', 'packaging', 'damage', 'general'])->default('materials');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_receipt_photos');
    }
};
