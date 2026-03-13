<?php

namespace App\Services\Locations;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class LocationSnapshotImporter
{
    /**
     * @return array{cities:int,districts:int,neighborhoods:int}
     */
    public function import(string $snapshotDir, bool $truncate = false): array
    {
        $manifestPath = rtrim($snapshotDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'manifest_v1.json';
        if (!is_file($manifestPath)) {
            throw new RuntimeException("manifest bulunamadi: $manifestPath");
        }

        $manifestRaw = file_get_contents($manifestPath);
        $manifest = json_decode((string) $manifestRaw, true);
        if (!is_array($manifest)) {
            throw new RuntimeException('manifest json parse edilemedi.');
        }

        $outputs = (array) ($manifest['outputs'] ?? []);
        $citiesCsv = $this->resolveOutputPath($snapshotDir, (string) ($outputs['cities_csv'] ?? 'cities_v1.csv'));
        $districtsCsv = $this->resolveOutputPath($snapshotDir, (string) ($outputs['districts_csv'] ?? 'districts_v1.csv'));
        $neighborhoodsCsv = $this->resolveOutputPath($snapshotDir, (string) ($outputs['neighborhoods_csv'] ?? 'neighborhoods_v1.csv'));

        $this->assertSha($citiesCsv, (string) ($outputs['cities_sha256'] ?? ''));
        $this->assertSha($districtsCsv, (string) ($outputs['districts_sha256'] ?? ''));
        $this->assertSha($neighborhoodsCsv, (string) ($outputs['neighborhoods_sha256'] ?? ''));

        $cityRows = $this->readCsvAssoc($citiesCsv);
        $districtRows = $this->readCsvAssoc($districtsCsv);
        $neighborhoodRows = $this->readCsvAssoc($neighborhoodsCsv);

        DB::transaction(function () use ($truncate, $cityRows, $districtRows, $neighborhoodRows): void {
            if ($truncate) {
                DB::table('neighborhoods')->delete();
                DB::table('districts')->delete();
                DB::table('cities')->delete();
            }

            foreach ($cityRows as $row) {
                DB::table('cities')->updateOrInsert(
                    ['slug' => (string) $row['city_slug']],
                    [
                        'name' => (string) $row['city_name'],
                        'sort_order' => (int) ($row['sort_order'] ?? 100),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $cityMap = DB::table('cities')->pluck('id', 'slug')->all();

            foreach ($districtRows as $row) {
                $citySlug = (string) $row['city_slug'];
                $cityId = (int) ($cityMap[$citySlug] ?? 0);
                if ($cityId <= 0) {
                    continue;
                }

                DB::table('districts')->updateOrInsert(
                    ['city_id' => $cityId, 'slug' => (string) $row['district_slug']],
                    [
                        'name' => (string) $row['district_name'],
                        'sort_order' => (int) ($row['sort_order'] ?? 100),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $districtMap = [];
            DB::table('districts')
                ->join('cities', 'districts.city_id', '=', 'cities.id')
                ->select([
                    'districts.id as district_id',
                    'cities.slug as city_slug',
                    'districts.slug as district_slug',
                ])
                ->orderBy('districts.id')
                ->get()
                ->each(function ($row) use (&$districtMap): void {
                    $key = (string) $row->city_slug . '|' . (string) $row->district_slug;
                    $districtMap[$key] = (int) $row->district_id;
                });

            foreach ($neighborhoodRows as $row) {
                $citySlug = (string) $row['city_slug'];
                $districtSlug = (string) $row['district_slug'];
                $key = $citySlug . '|' . $districtSlug;
                $districtId = (int) ($districtMap[$key] ?? 0);
                $cityId = (int) ($cityMap[$citySlug] ?? 0);
                if ($districtId <= 0 || $cityId <= 0) {
                    continue;
                }

                DB::table('neighborhoods')->updateOrInsert(
                    ['district_id' => $districtId, 'slug' => (string) $row['neighborhood_slug']],
                    [
                        'city_id' => $cityId,
                        'name' => (string) $row['neighborhood_name'],
                        'sort_order' => (int) ($row['sort_order'] ?? 100),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        return [
            'cities' => (int) DB::table('cities')->count(),
            'districts' => (int) DB::table('districts')->count(),
            'neighborhoods' => (int) DB::table('neighborhoods')->count(),
        ];
    }

    private function resolveOutputPath(string $snapshotDir, string $fileName): string
    {
        $path = rtrim($snapshotDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($path)) {
            throw new RuntimeException("snapshot cikti dosyasi bulunamadi: $fileName");
        }
        return $path;
    }

    private function assertSha(string $path, string $expected): void
    {
        if ($expected === '') {
            throw new RuntimeException("manifest hash bos: $path");
        }
        $actual = strtolower((string) hash_file('sha256', $path));
        if ($actual !== strtolower($expected)) {
            throw new RuntimeException("checksum uyusmuyor: $path");
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readCsvAssoc(string $path): array
    {
        $h = fopen($path, 'rb');
        if ($h === false) {
            throw new RuntimeException("csv acilamadi: $path");
        }

        $rows = [];
        $headers = fgetcsv($h);
        if (!is_array($headers)) {
            fclose($h);
            return [];
        }

        $headers = array_map(static fn($v) => trim((string) $v), $headers);
        while (($line = fgetcsv($h)) !== false) {
            if (!is_array($line)) {
                continue;
            }

            $row = [];
            foreach ($headers as $idx => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = trim((string) ($line[$idx] ?? ''));
            }

            if ($row === []) {
                continue;
            }
            $rows[] = $row;
        }

        fclose($h);
        return $rows;
    }
}

