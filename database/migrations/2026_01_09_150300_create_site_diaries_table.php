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
        Schema::create('site_diaries', function (Blueprint $table) {
            $table->id();
            $table->string('diary_number')->unique();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->date('diary_date');
            $table->enum('weather_morning', ['sunny', 'cloudy', 'rainy', 'windy', 'stormy'])->nullable();
            $table->enum('weather_afternoon', ['sunny', 'cloudy', 'rainy', 'windy', 'stormy'])->nullable();
            $table->decimal('temperature_min', 5, 2)->nullable();
            $table->decimal('temperature_max', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->enum('site_condition', ['dry', 'wet', 'muddy', 'flooded'])->nullable();
            $table->enum('work_status', ['normal', 'delayed', 'suspended', 'holiday'])->default('normal');
            $table->text('delay_reason')->nullable();
            $table->foreignId('prepared_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['project_id', 'diary_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_diaries');
    }
};
