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
        Schema::create('photo_annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->enum('annotation_type', ['arrow', 'circle', 'rectangle', 'text', 'marker'])->default('marker');
            $table->json('coordinates'); // {x, y, width, height}
            $table->string('color')->default('#FF0000');
            $table->text('text')->nullable();
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_annotations');
    }
};
