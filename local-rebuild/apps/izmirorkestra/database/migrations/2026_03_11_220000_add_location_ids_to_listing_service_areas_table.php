<?php

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('listing_service_areas', 'city_id')) {
            Schema::table('listing_service_areas', function (Blueprint $table) {
                $table->foreignId('city_id')->nullable()->after('listing_id')->constrained('cities')->nullOnDelete();
                $table->index(['city_id'], 'listing_service_areas_city_id_index');
            });
        }

        if (!Schema::hasColumn('listing_service_areas', 'district_id')) {
            Schema::table('listing_service_areas', function (Blueprint $table) {
                $table->foreignId('district_id')->nullable()->after('city_id')->constrained('districts')->nullOnDelete();
                $table->index(['district_id'], 'listing_service_areas_district_id_index');
                $table->index(['city_id', 'district_id'], 'listing_service_areas_city_district_id_index');
            });
        }

        $this->backfillLocationIds();
    }

    public function down(): void
    {
        Schema::table('listing_service_areas', function (Blueprint $table) {
            $sm = Schema::getConnection()->getSchemaBuilder();
            $indexes = $sm->getIndexes('listing_service_areas');
            foreach ([
                'listing_service_areas_city_district_id_index',
                'listing_service_areas_district_id_index',
                'listing_service_areas_city_id_index',
            ] as $indexName) {
                if (isset($indexes[$indexName])) {
                    $table->dropIndex($indexName);
                }
            }

            if (Schema::hasColumn('listing_service_areas', 'district_id')) {
                $table->dropConstrainedForeignId('district_id');
            }
            if (Schema::hasColumn('listing_service_areas', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
        });
    }

    private function backfillLocationIds(): void
    {
        if (!Schema::hasTable('cities') || !Schema::hasTable('districts')) {
            return;
        }

        $cityMap = City::query()
            ->select(['id', 'name'])
            ->get()
            ->keyBy(fn (City $row): string => mb_strtolower(trim((string) $row->name)));

        $districtMap = District::query()
            ->select(['id', 'city_id', 'name'])
            ->get()
            ->groupBy('city_id')
            ->map(function ($rows) {
                return $rows->keyBy(fn (District $row): string => mb_strtolower(trim((string) $row->name)));
            });

        DB::table('listing_service_areas')
            ->select(['id', 'city', 'district'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($cityMap, $districtMap): void {
                foreach ($rows as $row) {
                    $cityName = trim((string) ($row->city ?? ''));
                    $districtName = trim((string) ($row->district ?? ''));
                    if ($cityName === '') {
                        continue;
                    }

                    $city = $cityMap->get(mb_strtolower($cityName));
                    if (!$city) {
                        continue;
                    }

                    $cityId = (int) $city->id;
                    $districtId = null;
                    if ($districtName !== '') {
                        $districtRows = $districtMap->get($cityId);
                        $district = $districtRows?->get(mb_strtolower($districtName));
                        if ($district) {
                            $districtId = (int) $district->id;
                            $districtName = (string) $district->name;
                        }
                    }

                    DB::table('listing_service_areas')
                        ->where('id', (int) $row->id)
                        ->update([
                            'city_id' => $cityId,
                            'district_id' => $districtId,
                            'city' => (string) $city->name,
                            'district' => $districtName,
                            'updated_at' => now(),
                        ]);
                }
            });
    }
};
