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
        Schema::create('boq_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_header_id')->constrained()->cascadeOnDelete();
            $table->integer('revision_number');
            $table->text('revision_reason');
            $table->decimal('old_total', 18, 2);
            $table->decimal('new_total', 18, 2);
            $table->decimal('difference', 18, 2);
            $table->json('changes')->nullable(); // تفاصيل التغييرات
            $table->foreignId('revised_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_revisions');
    }
};
