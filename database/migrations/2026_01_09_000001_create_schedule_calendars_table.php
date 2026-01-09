<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('working_days')->nullable(); // [1,2,3,4,5]
            $table->json('working_hours')->nullable(); // {start: "08:00", end: "17:00", break_start: "12:00", break_end: "13:00"}
            $table->decimal('hours_per_day', 5, 2)->default(8.00);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('is_default');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_calendars');
    }
};
