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
        // Add new fields to guarantees table if they don't exist
        if (!Schema::hasColumn('guarantees', 'currency_id')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->foreignId('currency_id')->nullable()->after('amount')
                    ->constrained('currencies')->onDelete('restrict');
            });
        }

        if (!Schema::hasColumn('guarantees', 'exchange_rate')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->decimal('exchange_rate', 12, 6)->default(1.000000)->after('currency_id');
            });
        }

        if (!Schema::hasColumn('guarantees', 'amount_words')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->text('amount_words')->nullable()->after('amount_in_base_currency');
            });
        }

        if (!Schema::hasColumn('guarantees', 'amount_words_en')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->text('amount_words_en')->nullable()->after('amount_words');
            });
        }

        if (!Schema::hasColumn('guarantees', 'contractor_name')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('contractor_name')->nullable()->after('amount_words_en');
            });
        }

        if (!Schema::hasColumn('guarantees', 'contractor_cr')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('contractor_cr')->nullable()->after('contractor_name');
            });
        }

        if (!Schema::hasColumn('guarantees', 'contractor_address')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->text('contractor_address')->nullable()->after('contractor_cr');
            });
        }

        if (!Schema::hasColumn('guarantees', 'beneficiary_name')) {
            Schema::table('guarantees', function (Blueprint $table) {
                // Rename beneficiary to beneficiary_name if beneficiary exists
                if (Schema::hasColumn('guarantees', 'beneficiary')) {
                    $table->renameColumn('beneficiary', 'beneficiary_name');
                } else {
                    $table->string('beneficiary_name')->nullable()->after('contractor_address');
                }
            });
        }

        if (!Schema::hasColumn('guarantees', 'contract_number')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('contract_number')->nullable()->after('contract_id');
            });
        }

        if (!Schema::hasColumn('guarantees', 'lg_number')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('lg_number')->nullable()->after('bank_reference_number');
            });
        }

        if (!Schema::hasColumn('guarantees', 'description')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->text('description')->nullable()->after('purpose');
            });
        }

        if (!Schema::hasColumn('guarantees', 'branch_id')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('project_id')
                    ->constrained('branches')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('guarantees', 'template_id')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->foreignId('template_id')->nullable()->after('status')
                    ->constrained('payment_templates')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('guarantees', 'start_date')) {
            Schema::table('guarantees', function (Blueprint $table) {
                // Rename issue_date to start_date if issue_date exists
                if (Schema::hasColumn('guarantees', 'issue_date')) {
                    $table->renameColumn('issue_date', 'start_date');
                } else {
                    $table->date('start_date')->nullable()->after('template_id');
                }
            });
        }

        if (!Schema::hasColumn('guarantees', 'end_date')) {
            Schema::table('guarantees', function (Blueprint $table) {
                // Rename expiry_date to end_date if expiry_date exists
                if (Schema::hasColumn('guarantees', 'expiry_date')) {
                    $table->renameColumn('expiry_date', 'end_date');
                } else {
                    $table->date('end_date')->nullable()->after('start_date');
                }
            });
        }

        if (!Schema::hasColumn('guarantees', 'released_at')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->timestamp('released_at')->nullable()->after('approved_at');
            });
        }

        if (!Schema::hasColumn('guarantees', 'released_by')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->foreignId('released_by')->nullable()->after('released_at')
                    ->constrained('users')->onDelete('set null');
            });
        }

        // Update guarantee_type enum values if needed
        if (Schema::hasColumn('guarantees', 'type')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('guarantee_type')->nullable()->after('guarantee_number');
            });
            
            // Copy data from type to guarantee_type
            DB::statement('UPDATE guarantees SET guarantee_type = type');
            
            Schema::table('guarantees', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('guarantees', function (Blueprint $table) {
                $table->renameColumn('guarantee_type', 'type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guarantees', function (Blueprint $table) {
            $columns = [
                'currency_id', 'exchange_rate', 'amount_words', 'amount_words_en',
                'contractor_name', 'contractor_cr', 'contractor_address',
                'contract_number', 'lg_number', 'description', 'branch_id',
                'template_id', 'released_at', 'released_by'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('guarantees', $column)) {
                    if (in_array($column, ['currency_id', 'branch_id', 'template_id', 'released_by'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
