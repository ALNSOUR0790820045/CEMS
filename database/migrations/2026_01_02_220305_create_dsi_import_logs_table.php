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
        Schema::create('dsi_import_logs', function (Blueprint $table) {
            $table->id();
            $table->date('import_date');
            $table->integer('records_imported');
            $table->string('file_path')->nullable();
            $table->foreignId('imported_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dsi_import_logs');
    }
};
