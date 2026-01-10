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
        // Check if columns don't exist before adding them
        if (!Schema::hasColumn('currencies', 'symbol_position')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->enum('symbol_position', ['before', 'after'])->default('after')->after('symbol');
            });
        }

        if (!Schema::hasColumn('currencies', 'decimal_places')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->tinyInteger('decimal_places')->default(2)->after('symbol_position');
            });
        }

        if (!Schema::hasColumn('currencies', 'thousands_separator')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->string('thousands_separator', 1)->default(',')->after('decimal_places');
            });
        }

        if (!Schema::hasColumn('currencies', 'decimal_separator')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->string('decimal_separator', 1)->default('.')->after('thousands_separator');
            });
        }

        if (!Schema::hasColumn('currencies', 'is_base')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->boolean('is_base')->default(false)->after('decimal_separator');
            });
        }

        if (!Schema::hasColumn('currencies', 'last_updated')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->timestamp('last_updated')->nullable()->after('exchange_rate');
            });
        }

        // Modify exchange_rate precision if needed
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('exchange_rate', 10, 6)->default(1.000000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            if (Schema::hasColumn('currencies', 'symbol_position')) {
                $table->dropColumn('symbol_position');
            }
            if (Schema::hasColumn('currencies', 'decimal_places')) {
                $table->dropColumn('decimal_places');
            }
            if (Schema::hasColumn('currencies', 'thousands_separator')) {
                $table->dropColumn('thousands_separator');
            }
            if (Schema::hasColumn('currencies', 'decimal_separator')) {
                $table->dropColumn('decimal_separator');
            }
            if (Schema::hasColumn('currencies', 'is_base')) {
                $table->dropColumn('is_base');
            }
            if (Schema::hasColumn('currencies', 'last_updated')) {
                $table->dropColumn('last_updated');
            }
        });
    }
};
