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
        Schema::create('guarantee_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guarantee_id')->constrained()->cascadeOnDelete();
            $table->date('old_expiry_date');
            $table->date('new_expiry_date');
            $table->decimal('renewal_charges', 10, 2)->default(0);
            $table->decimal('new_amount', 15, 2)->nullable();
            $table->date('renewal_date');
            $table->string('bank_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('renewed_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantee_renewals');
    }
};
