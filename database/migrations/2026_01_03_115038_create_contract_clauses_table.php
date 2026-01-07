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
        Schema::create('contract_clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            
            $table->string('clause_number'); // e.g., "5.2.1"
            $table->string('clause_title');
            $table->text('clause_content');
            
            $table->enum('clause_category', ['payment', 'penalties', 'warranties', 'termination', 'scope', 'time', 'quality', 'safety', 'other'])->default('other');
            
            $table->boolean('is_critical')->default(false);
            
            $table->integer('display_order')->default(0);
            
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_clauses');
    }
};
