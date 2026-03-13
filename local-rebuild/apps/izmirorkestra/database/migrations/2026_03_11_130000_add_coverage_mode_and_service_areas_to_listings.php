<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('listings', 'coverage_mode')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->string('coverage_mode', 24)->default('location_only')->after('site_scope');
                $table->index(['coverage_mode'], 'listings_coverage_mode_index');
            });
        }

        if (!Schema::hasTable('listing_service_areas')) {
            Schema::create('listing_service_areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
                $table->string('city', 120);
                $table->string('district', 120)->default('');
                $table->timestamps();

                $table->unique(['listing_id', 'city', 'district'], 'listing_service_areas_unique');
                $table->index(['city', 'district'], 'listing_service_areas_city_district_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('listing_service_areas')) {
            Schema::drop('listing_service_areas');
        }

        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'coverage_mode')) {
                $sm = Schema::getConnection()->getSchemaBuilder();
                $tableIndexes = $sm->getIndexes('listings');
                if (isset($tableIndexes['listings_coverage_mode_index'])) {
                    $table->dropIndex('listings_coverage_mode_index');
                }
                $table->dropColumn('coverage_mode');
            }
        });
    }
};
