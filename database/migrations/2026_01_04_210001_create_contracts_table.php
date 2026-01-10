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
            $table->string('title');
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('contract_type', ['FIDIC', 'NEC', 'JCT', 'custom'])->default('FIDIC');
            $table->date('signing_date')->nullable();
            $table->date('commencement_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->string('currency', 3)->default('JOD');
            $table->integer('default_notice_period')->default(28);
            $table->enum('status', ['draft', 'active', 'suspended', 'completed', 'terminated'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
