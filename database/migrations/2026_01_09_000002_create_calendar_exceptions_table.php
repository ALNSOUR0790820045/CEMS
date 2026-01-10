<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_calendar_id')->constrained('schedule_calendars')->cascadeOnDelete();
            $table->date('exception_date');
            $table->enum('exception_type', ['holiday', 'non_working', 'extra_working']);
            $table->string('name');
            $table->json('working_hours')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_pattern', ['yearly', 'none'])->default('none');
            $table->timestamps();
            
            $table->index(['schedule_calendar_id', 'exception_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_exceptions');
    }
};
