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
        // labor_camps table (سكن العمال)
        Schema::create('labor_camps', function (Blueprint $table) {
            $table->id();
            $table->string('camp_number')->unique();
            $table->string('name');
            $table->string('location');
            $table->foreignId('project_id')->nullable()->constrained();
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);
            $table->string('supervisor')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('facilities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_camps');
    }
};
