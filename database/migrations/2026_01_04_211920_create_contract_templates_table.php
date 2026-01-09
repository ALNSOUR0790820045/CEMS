<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // JEA-01, JEA-02, FIDIC-RED
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', [
                'jea_01',           // عقد نقابة المقاولين - أعمال بناء
                'jea_02',           // عقد نقابة المقاولين - أعمال ميكانيك
                'fidic_red',        // فيديك الأحمر
                'fidic_yellow',     // فيديك الأصفر
                'fidic_silver',     // فيديك الفضي
                'ministry',         // وزارة الأشغال
                'custom'            // مخصص
            ]);
            $table->string('version')->nullable();
            $table->integer('year')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_templates');
    }
};
