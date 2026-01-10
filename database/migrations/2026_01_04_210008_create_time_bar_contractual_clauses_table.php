<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_bar_contractual_clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('clause_number'); // مثل 20.1
            $table->string('clause_title');
            $table->text('clause_text');
            $table->integer('notice_period_days');
            $table->text('notice_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index
            $table->index(['contract_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_bar_contractual_clauses');
    }
};
