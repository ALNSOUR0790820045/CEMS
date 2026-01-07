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
        Schema::create('guarantee_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guarantee_id')->constrained()->cascadeOnDelete();
            $table->date('release_date');
            $table->decimal('released_amount', 15, 2);
            $table->enum('release_type', ['full', 'partial'])->default('full');
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->string('release_document')->nullable();
            $table->string('bank_confirmation_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('released_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantee_releases');
    }
};
