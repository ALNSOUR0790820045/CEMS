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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 1-1-001
            $table->string('name'); // الصندوق
            $table->string('name_en')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('nature', ['debit', 'credit']); // طبيعة الحساب
            $table->integer('level')->default(1); // مستوى الحساب
            $table->boolean('is_parent')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
