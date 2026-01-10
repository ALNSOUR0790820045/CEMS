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
        Schema::create('diary_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained('site_diaries')->onDelete('cascade');
            $table->enum('instruction_type', ['client', 'consultant', 'internal', 'safety'])->default('internal');
            $table->string('issued_by');
            $table->foreignId('received_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description');
            $table->text('action_required')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_instructions');
    }
};
