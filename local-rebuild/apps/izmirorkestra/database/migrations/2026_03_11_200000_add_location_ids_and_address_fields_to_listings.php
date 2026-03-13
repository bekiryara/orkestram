<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('listings', 'city_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->foreignId('city_id')->nullable()->after('category_id')->constrained('cities')->nullOnDelete();
                $table->index(['city_id'], 'listings_city_id_index');
            });
        }

        if (!Schema::hasColumn('listings', 'district_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->foreignId('district_id')->nullable()->after('city_id')->constrained('districts')->nullOnDelete();
                $table->index(['district_id'], 'listings_district_id_index');
            });
        }

        if (!Schema::hasColumn('listings', 'neighborhood_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->foreignId('neighborhood_id')->nullable()->after('district_id')->constrained('neighborhoods')->nullOnDelete();
                $table->index(['neighborhood_id'], 'listings_neighborhood_id_index');
            });
        }

        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'street_name')) {
                $table->string('street_name', 180)->nullable()->after('district');
            }
            if (!Schema::hasColumn('listings', 'building_no')) {
                $table->string('building_no', 40)->nullable()->after('street_name');
            }
            if (!Schema::hasColumn('listings', 'unit_no')) {
                $table->string('unit_no', 40)->nullable()->after('building_no');
            }
            if (!Schema::hasColumn('listings', 'address_note')) {
                $table->string('address_note', 255)->nullable()->after('unit_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $sm = Schema::getConnection()->getSchemaBuilder();
            $indexes = $sm->getIndexes('listings');

            foreach ([
                'listings_neighborhood_id_index',
                'listings_district_id_index',
                'listings_city_id_index',
            ] as $indexName) {
                if (isset($indexes[$indexName])) {
                    $table->dropIndex($indexName);
                }
            }

            if (Schema::hasColumn('listings', 'neighborhood_id')) {
                $table->dropConstrainedForeignId('neighborhood_id');
            }
            if (Schema::hasColumn('listings', 'district_id')) {
                $table->dropConstrainedForeignId('district_id');
            }
            if (Schema::hasColumn('listings', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }

            foreach (['address_note', 'unit_no', 'building_no', 'street_name'] as $column) {
                if (Schema::hasColumn('listings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

