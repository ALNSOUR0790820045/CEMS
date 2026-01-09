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
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_number')->unique();
            $table->foreignId('fixed_asset_id')->constrained()->onDelete('restrict');
            $table->date('disposal_date');
            $table->enum('disposal_type', ['sale', 'write_off', 'donation', 'scrap']);
            $table->text('disposal_reason')->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->decimal('accumulated_depreciation_at_disposal', 15, 2);
            $table->decimal('net_book_value_at_disposal', 15, 2);
            $table->decimal('gain_loss', 15, 2)->nullable()->comment('Positive for gain, negative for loss');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_contact')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
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
        Schema::dropIfExists('asset_disposals');
    }
};
