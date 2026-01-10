<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variation_orders', function (Blueprint $table) {
            $table->id();
            $table->string('vo_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->decimal('value', 18, 2)->default(0);
            $table->string('currency', 3)->default('JOD');
            $table->integer('time_impact_days')->default(0);
            $table->enum('status', ['proposed', 'approved', 'rejected', 'implemented'])->default('proposed');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variation_orders');
    }
};
