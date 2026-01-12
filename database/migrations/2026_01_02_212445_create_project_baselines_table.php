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
        Schema::create('project_baselines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('baseline_name'); // Initial, Revised 1, etc.
            $table->date('baseline_date');
            $table->boolean('is_current')->default(false);
            
            // لقطة من الجدول الزمني والميزانية
            $table->json('schedule_snapshot'); // جميع الأنشطة والتواريخ
            $table->json('cost_snapshot'); // جميع التكاليف المخططة
            
            $table->text('reason')->nullable(); // سبب التعديل
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_baselines');
    }
};
