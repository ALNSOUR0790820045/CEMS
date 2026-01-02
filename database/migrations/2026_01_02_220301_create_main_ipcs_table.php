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
        Schema::create('main_ipcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('ipc_number')->unique(); // IPC-001
            $table->date('ipc_date');
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('amount', 15, 2);
            $table->decimal('previous_total', 15, 2)->default(0);
            $table->decimal('current_total', 15, 2);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'paid', 'rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_ipcs');
    }
};
