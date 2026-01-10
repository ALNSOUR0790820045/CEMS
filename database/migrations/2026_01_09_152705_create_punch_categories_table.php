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
        Schema::create('punch_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('punch_categories')->nullOnDelete();
            $table->string('discipline')->nullable();
            $table->string('color', 7)->nullable(); // Hex color code
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_categories');
    }
};
