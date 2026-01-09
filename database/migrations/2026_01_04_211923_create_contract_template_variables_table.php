<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_template_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('contract_templates')->cascadeOnDelete();
            $table->string('variable_key'); // {{employer_name}}, {{contract_value}}
            $table->string('variable_label');
            $table->string('variable_label_en')->nullable();
            $table->enum('data_type', ['text', 'number', 'date', 'currency', 'percentage']);
            $table->boolean('is_required')->default(true);
            $table->string('default_value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_template_variables');
    }
};
