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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ريال سعودي
            $table->string('name_en')->nullable(); // Saudi Riyal
            $table->string('code', 3)->unique(); // SAR
            $table->string('symbol', 10); // ر.س
            $table->decimal('exchange_rate', 15, 6)->default(1); // سعر الصرف
            $table->boolean('is_base')->default(false); // عملة أساسية؟
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
