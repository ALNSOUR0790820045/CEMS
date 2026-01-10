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
        Schema::create('diary_manpower', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained('site_diaries')->onDelete('cascade');
            $table->enum('trade', ['carpenter', 'mason', 'electrician', 'plumber', 'steel_fixer', 'painter', 'laborer', 'foreman', 'engineer', 'supervisor', 'driver', 'operator', 'welder', 'other']);
            $table->integer('own_count')->default(0);
            $table->integer('subcontractor_count')->default(0);
            $table->foreignId('subcontractor_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('hours_worked', 5, 2)->default(8.00);
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_manpower');
    }
};
