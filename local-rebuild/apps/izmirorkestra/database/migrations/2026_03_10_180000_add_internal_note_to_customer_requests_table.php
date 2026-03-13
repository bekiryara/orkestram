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

        if (Schema::hasColumn('customer_requests', 'internal_note')) {
            return;
        }

        try {
            Schema::table('customer_requests', function (Blueprint $table) {
                $table->text('internal_note')->nullable()->after('status');
            });
        } catch (QueryException $e) {
            // Idempotent safety: ignore duplicate-column race/state drift.
            if ((int) $e->getCode() !== 42_000 && ! str_contains(strtolower($e->getMessage()), 'duplicate column')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_requests') && Schema::hasColumn('customer_requests', 'internal_note')) {
            Schema::table('customer_requests', function (Blueprint $table) {
                $table->dropColumn('internal_note');
            });
        }
    }
};
