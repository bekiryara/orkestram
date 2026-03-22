<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customer_requests')) {
            return;
        }

        $missing = array_values(array_filter([
            'pricing_mode',
            'price_type',
            'price_min',
            'price_max',
            'currency',
            'price_label',
        ], fn (string $column): bool => ! Schema::hasColumn('customer_requests', $column)));

        if ($missing === []) {
            return;
        }

        try {
            Schema::table('customer_requests', function (Blueprint $table) use ($missing) {
                if (in_array('pricing_mode', $missing, true)) {
                    $table->string('pricing_mode', 32)->nullable()->after('listing_id');
                }
                if (in_array('price_type', $missing, true)) {
                    $table->string('price_type', 32)->nullable()->after('pricing_mode');
                }
                if (in_array('price_min', $missing, true)) {
                    $table->decimal('price_min', 10, 2)->nullable()->after('price_type');
                }
                if (in_array('price_max', $missing, true)) {
                    $table->decimal('price_max', 10, 2)->nullable()->after('price_min');
                }
                if (in_array('currency', $missing, true)) {
                    $table->string('currency', 3)->nullable()->after('price_max');
                }
                if (in_array('price_label', $missing, true)) {
                    $table->string('price_label', 255)->nullable()->after('currency');
                }
            });
        } catch (QueryException $e) {
            if ((int) $e->getCode() !== 42000 && ! str_contains(strtolower($e->getMessage()), 'duplicate column')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('customer_requests')) {
            return;
        }

        $existing = array_values(array_filter([
            'pricing_mode',
            'price_type',
            'price_min',
            'price_max',
            'currency',
            'price_label',
        ], fn (string $column): bool => Schema::hasColumn('customer_requests', $column)));

        if ($existing === []) {
            return;
        }

        Schema::table('customer_requests', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
};
