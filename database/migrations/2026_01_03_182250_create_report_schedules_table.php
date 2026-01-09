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
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->time('schedule_time')->nullable();
            $table->integer('schedule_day')->nullable();
            $table->json('email_recipients')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};
