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
        Schema::create('defect_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defects_liability_id')->constrained()->cascadeOnDelete();
            $table->string('notification_number')->unique();
            $table->date('notification_date');
            $table->string('notified_by');
            $table->text('defect_description');
            $table->string('location')->nullable();
            $table->enum('severity', ['minor', 'major', 'critical']);
            $table->date('rectification_deadline')->nullable();
            $table->date('rectification_date')->nullable();
            $table->enum('status', ['notified', 'acknowledged', 'in_progress', 'rectified', 'disputed'])->default('notified');
            $table->decimal('cost_to_rectify', 15, 2)->nullable();
            $table->decimal('deducted_from_retention', 15, 2)->default(0);
            $table->json('photos')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defect_notifications');
    }
};
