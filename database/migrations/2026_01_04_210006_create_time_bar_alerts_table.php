<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_bar_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('time_bar_events')->cascadeOnDelete();
            
            $table->enum('alert_type', [
                'first_warning',    // تنبيه أول (21 يوم متبقي)
                'second_warning',   // تنبيه ثاني (14 يوم متبقي)
                'urgent_warning',   // تنبيه عاجل (7 أيام متبقية)
                'critical_warning', // تنبيه حرج (3 أيام متبقية)
                'final_warning',    // تنبيه أخير (1 يوم متبقي)
                'expired'           // انتهى الموعد!
            ]);
            
            $table->integer('days_remaining');
            $table->datetime('sent_at');
            $table->json('sent_to'); // قائمة المستلمين
            $table->enum('channel', ['system', 'email', 'sms', 'all'])->default('all');
            $table->boolean('acknowledged')->default(false);
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('alert_type');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_bar_alerts');
    }
};
