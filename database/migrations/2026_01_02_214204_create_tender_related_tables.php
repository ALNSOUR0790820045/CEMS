<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // زيارات الموقع
        Schema::create('tender_site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            
            $table->json('attendees'); // الحاضرون من فريقنا
            $table->text('observations')->nullable();
            $table->json('photos')->nullable(); // صور الموقع
            $table->json('coordinates')->nullable(); // GPS
            
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamps();
        });

        // الاستفسارات والأجوبة
        Schema::create('tender_clarifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->date('question_date');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->date('answer_date')->nullable();
            $table->enum('status', ['pending', 'answered'])->default('pending');
            $table->foreignId('asked_by')->constrained('users');
            $table->timestamps();
        });

        // المنافسون
        Schema::create('tender_competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->enum('classification', ['strong', 'medium', 'weak'])->default('medium');
            $table->decimal('estimated_price', 15, 2)->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // قرارات اللجنة
        Schema::create('tender_committee_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->date('meeting_date');
            $table->json('attendees');
            $table->enum('decision', ['go', 'no_go', 'pending'])->default('pending');
            $table->text('reasons')->nullable();
            $table->text('conditions')->nullable();
            $table->decimal('approved_budget', 15, 2)->nullable();
            $table->foreignId('chairman_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_committee_decisions');
        Schema::dropIfExists('tender_competitors');
        Schema::dropIfExists('tender_clarifications');
        Schema::dropIfExists('tender_site_visits');
    }
};
