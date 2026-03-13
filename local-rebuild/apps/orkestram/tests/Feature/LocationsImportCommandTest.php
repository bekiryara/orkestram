<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LocationsImportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_locations_import_reads_snapshot_and_writes_dictionary_tables(): void
    {
        $snapshotDir = storage_path('framework/testing/locations_v1');
        if (!is_dir($snapshotDir)) {
            mkdir($snapshotDir, 0775, true);
        }

        $citiesCsv = $snapshotDir . DIRECTORY_SEPARATOR . 'cities_v1.csv';
        $districtsCsv = $snapshotDir . DIRECTORY_SEPARATOR . 'districts_v1.csv';
        $neighborhoodsCsv = $snapshotDir . DIRECTORY_SEPARATOR . 'neighborhoods_v1.csv';

        file_put_contents($citiesCsv, implode("\n", [
            'city_id,city_name,city_slug,sort_order',
            '1,Izmir,izmir,10',
        ]));
        file_put_contents($districtsCsv, implode("\n", [
            'district_id,city_id,city_name,city_slug,district_name,district_slug,sort_order',
            '1,1,Izmir,izmir,Konak,konak,10',
        ]));
        file_put_contents($neighborhoodsCsv, implode("\n", [
            'neighborhood_id,district_id,city_id,city_name,city_slug,district_name,district_slug,neighborhood_name,neighborhood_slug,sort_order',
            '1,1,1,Izmir,izmir,Konak,konak,Alsancak,alsancak,10',
            '2,1,1,Izmir,izmir,Konak,konak,Kultur,kultur,20',
        ]));

        $manifest = [
            'version' => 'locations_v1',
            'outputs' => [
                'cities_csv' => 'cities_v1.csv',
                'districts_csv' => 'districts_v1.csv',
                'neighborhoods_csv' => 'neighborhoods_v1.csv',
                'cities_sha256' => hash_file('sha256', $citiesCsv),
                'districts_sha256' => hash_file('sha256', $districtsCsv),
                'neighborhoods_sha256' => hash_file('sha256', $neighborhoodsCsv),
            ],
            'stats' => [
                'city_count' => 1,
                'district_count' => 1,
                'neighborhood_count' => 2,
                'anomaly_count' => 0,
            ],
        ];
        file_put_contents(
            $snapshotDir . DIRECTORY_SEPARATOR . 'manifest_v1.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $exitCode = Artisan::call('locations:import', [
            '--from' => $snapshotDir,
            '--truncate' => true,
        ]);
        $this->assertSame(0, $exitCode, Artisan::output());

        $this->assertDatabaseHas('cities', ['slug' => 'izmir', 'name' => 'Izmir']);
        $this->assertDatabaseHas('districts', ['slug' => 'konak', 'name' => 'Konak']);
        $this->assertDatabaseHas('neighborhoods', ['slug' => 'alsancak', 'name' => 'Alsancak']);
        $this->assertDatabaseHas('neighborhoods', ['slug' => 'kultur', 'name' => 'Kultur']);
    }
}

