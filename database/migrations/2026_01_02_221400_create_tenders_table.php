<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('submission_date')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->enum('status', ['draft', 'submitted', 'won', 'lost'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
