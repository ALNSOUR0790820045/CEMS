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
        Schema::create('certification_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('renewal_number')->unique();
            $table->foreignId('certification_id')->constrained()->cascadeOnDelete();
            $table->date('old_expiry_date');
            $table->date('new_expiry_date');
            $table->decimal('renewal_cost', 18, 2)->nullable();
            $table->date('renewal_date');
            $table->foreignId('processed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['certification_id']);
            $table->index(['renewal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification_renewals');
    }
};
