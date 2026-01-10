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
        Schema::create('diary_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained()->onDelete('cascade');
            $table->string('visitor_name');
            $table->string('organization')->nullable();
            $table->string('designation')->nullable();
            $table->enum('purpose', ['inspection', 'meeting', 'audit', 'delivery', 'other'])->default('other');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('escorted_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_visitors');
    }
};
