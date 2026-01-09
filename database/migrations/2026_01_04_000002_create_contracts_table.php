<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('contractor_name');
            $table->decimal('contract_value', 18, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->enum('status', ['draft', 'active', 'suspended', 'terminated', 'completed'])->default('draft');
            $table->string('currency', 3)->default('JOD');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
