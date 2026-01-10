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
        Schema::create('tender_activity_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predecessor_id')->constrained('tender_activities')->cascadeOnDelete();
            $table->foreignId('successor_id')->constrained('tender_activities')->cascadeOnDelete();
            $table->enum('type', ['FS', 'SS', 'FF', 'SF'])->default('FS'); // Finish-Start, Start-Start, Finish-Finish, Start-Finish
            $table->integer('lag_days')->default(0);
            $table->timestamps();
            
            $table->unique(['predecessor_id', 'successor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_activity_dependencies');
    }
};
