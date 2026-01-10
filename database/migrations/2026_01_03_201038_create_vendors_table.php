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
<<<<<<<< HEAD:database/migrations/2026_01_03_201038_create_vendors_table.php
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
========
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
>>>>>>>> origin/main:database/migrations/2026_01_02_121900_create_companies_table_fixed.php
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
<<<<<<<< HEAD:database/migrations/2026_01_03_201038_create_vendors_table.php
            $table->string('contact_person')->nullable();
            $table->string('tax_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
========
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->enum('type', ['materials', 'services', 'equipment', 'mixed'])->default('materials');
            $table->enum('payment_terms', ['cash', 'credit_30', 'credit_60', 'credit_90'])->default('credit_30');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
>>>>>>>> origin/main:database/migrations/2026_01_02_121900_create_companies_table_fixed.php
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2026_01_03_201038_create_vendors_table.php
        Schema::dropIfExists('vendors');
========
        Schema::dropIfExists('suppliers');
>>>>>>>> origin/main:database/migrations/2026_01_02_121900_create_companies_table_fixed.php
    }
};
