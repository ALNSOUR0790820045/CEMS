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
        Schema::create('activity_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predecessor_id')->constrained('project_activities')->cascadeOnDelete();
            $table->foreignId('successor_id')->constrained('project_activities')->cascadeOnDelete();
            $table->enum('type', ['FS', 'SS', 'FF', 'SF'])->default('FS'); // Finish-to-Start, etc.
            $table->integer('lag_days')->default(0); // تأخير/تقديم
            $table->timestamps();
            
            $table->unique(['predecessor_id', 'successor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_dependencies');
    }
};
