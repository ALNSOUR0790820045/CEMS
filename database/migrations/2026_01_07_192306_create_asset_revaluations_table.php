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
        Schema::create('asset_revaluations', function (Blueprint $table) {
            $table->id();
            $table->string('revaluation_number')->unique();
            $table->foreignId('fixed_asset_id')->constrained()->onDelete('restrict');
            $table->date('revaluation_date');
            $table->decimal('old_value', 15, 2);
            $table->decimal('new_value', 15, 2);
            $table->decimal('revaluation_surplus_deficit', 15, 2)->comment('Positive for surplus, negative for deficit');
            $table->text('reason')->nullable();
            $table->string('appraiser_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'posted'])->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('gl_journal_entry_id')->nullable()->constrained('gl_journal_entries')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_revaluations');
    }
};
