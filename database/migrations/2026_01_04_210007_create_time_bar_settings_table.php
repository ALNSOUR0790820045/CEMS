<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_bar_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->integer('default_notice_period')->default(28);
            $table->integer('first_warning_days')->default(21);
            $table->integer('second_warning_days')->default(14);
            $table->integer('urgent_warning_days')->default(7);
            $table->integer('critical_warning_days')->default(3);
            $table->integer('final_warning_days')->default(1);
            
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('escalation_enabled')->default(true);
            
            $table->json('notification_recipients')->nullable();
            $table->json('escalation_recipients')->nullable();
            
            $table->timestamps();
            
            // Unique constraint to ensure one setting per project/contract
            $table->unique(['project_id', 'contract_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_bar_settings');
    }
};
