<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'site_scope')) {
                $table->string('site_scope', 20)->default('single')->after('site');
            }
            if (!Schema::hasColumn('listings', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('status')->constrained('categories')->nullOnDelete();
            }

            $sm = Schema::getConnection()->getSchemaBuilder();
            $tableIndexes = $sm->getIndexes('listings');
            if (!isset($tableIndexes['listings_site_site_scope_status_index'])) {
                $table->index(['site', 'site_scope', 'status']);
            }
            if (!isset($tableIndexes['listings_category_id_status_index'])) {
                $table->index(['category_id', 'status']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $sm = Schema::getConnection()->getSchemaBuilder();
            $tableIndexes = $sm->getIndexes('listings');
            if (isset($tableIndexes['listings_site_site_scope_status_index'])) {
                $table->dropIndex(['site', 'site_scope', 'status']);
            }
            if (isset($tableIndexes['listings_category_id_status_index'])) {
                $table->dropIndex(['category_id', 'status']);
            }
            if (Schema::hasColumn('listings', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
            if (Schema::hasColumn('listings', 'site_scope')) {
                $table->dropColumn('site_scope');
            }
        });
    }
};
