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
        Schema::create('price_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained();
            $table->date('request_date');
            $table->date('required_by');
            $table->enum('status', ['draft', 'sent', 'received', 'analyzed', 'closed'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_requests');
    }
};
