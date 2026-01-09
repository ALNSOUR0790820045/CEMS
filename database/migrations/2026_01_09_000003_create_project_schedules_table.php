<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_number')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('schedule_type', ['baseline', 'current', 'revised', 'what_if'])->default('current');
            $table->integer('version')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->integer('working_days_per_week')->default(5);
            $table->decimal('hours_per_day', 5, 2)->default(8.00);
            $table->foreignId('calendar_id')->nullable()->constrained('schedule_calendars');
            $table->date('data_date')->nullable();
            $table->enum('status', ['draft', 'baseline', 'approved', 'superseded'])->default('draft');
            $table->foreignId('prepared_by_id')->constrained('users');
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('schedule_number');
            $table->index('project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_schedules');
    }
};
