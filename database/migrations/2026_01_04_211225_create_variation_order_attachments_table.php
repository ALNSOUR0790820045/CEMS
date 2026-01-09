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
        Schema::create('variation_order_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_order_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // instruction, drawing, photo, calculation, correspondence
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_order_attachments');
    }
};
