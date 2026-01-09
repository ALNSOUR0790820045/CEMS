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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type')->default('info'); // info, success, warning, error, reminder
            $table->string('category')->default('system'); // system, approval, deadline, alert, message
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('body');
            $table->text('body_en')->nullable();
            $table->json('data')->nullable();
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('action_url')->nullable();
            $table->string('icon')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('company_id');
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
