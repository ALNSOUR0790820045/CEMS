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
        Schema::create('equipment_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('from_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('to_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->string('transport_method')->nullable();
            $table->decimal('transport_cost', 15, 2)->default(0);
            $table->foreignId('approved_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_transfers');
    }
};
