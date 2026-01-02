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
        Schema::create('change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('co_number')->unique();
            $table->text('description');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('type', ['addition', 'deduction'])->default('addition');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->date('approval_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_orders');
    }
};
