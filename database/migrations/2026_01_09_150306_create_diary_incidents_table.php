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
        Schema::create('diary_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained('site_diaries')->onDelete('cascade');
            $table->enum('incident_type', ['accident', 'near_miss', 'property_damage', 'environmental', 'security']);
            $table->enum('severity', ['minor', 'moderate', 'major', 'critical'])->default('minor');
            $table->time('time_occurred')->nullable();
            $table->string('location')->nullable();
            $table->text('description');
            $table->text('persons_involved')->nullable();
            $table->text('injuries')->nullable();
            $table->text('property_damage')->nullable();
            $table->text('immediate_action')->nullable();
            $table->string('reported_to')->nullable();
            $table->boolean('hse_notified')->default(false);
            $table->boolean('investigation_required')->default(false);
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_incidents');
    }
};
