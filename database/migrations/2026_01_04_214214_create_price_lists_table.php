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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', [
                'material',     // مواد
                'labor',        // عمالة
                'equipment',    // معدات
                'subcontract',  // أعمال مقاولين
                'composite'     // مركب
            ]);
            $table->enum('source', [
                'internal',     // داخلي (الشركة)
                'ministry',     // وزارة الأشغال
                'syndicate',    // النقابة
                'market',       // السوق
                'vendor'        // مورد
            ])->default('internal');
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->string('currency', 3)->default('JOD');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
