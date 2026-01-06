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
        Schema::create('tender_wbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
            $table->string('wbs_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_wbs_id')->nullable()->constrained('tender_wbs')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->integer('sequence_order')->nullable();
            $table->timestamps();

            $table->index(['tender_id', 'parent_wbs_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_wbs');
    }
};
