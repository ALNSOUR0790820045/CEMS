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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('client_code')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('client_type', ['government', 'semi_government', 'private', 'individual'])->default('private');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
