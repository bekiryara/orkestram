<?php

namespace App\Services\Listings;

use App\Models\City;
use App\Models\District;
use App\Models\Listing;

class ListingCoverageService
{
    public function syncFromRawText(Listing $listing, ?string $raw): void
    {
        $areas = $this->parseRawText($raw);
        $areas = $this->attachLocationIds($areas);

        $listing->serviceAreas()->delete();
        if ($areas === []) {
            return;
        }

        $listing->serviceAreas()->createMany($areas);
    }

    /**
     * @return array<int, array{city:string,district:string}>
     */
    private function parseRawText(?string $raw): array
    {
        if ($raw === null) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $seen = [];
        $rows = [];

        foreach ($lines as $line) {
            [$city, $district] = $this->parseLine($line);
            if ($city === '') {
                continue;
            }

            $key = mb_strtolower($city) . '|' . mb_strtolower($district);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $rows[] = [
                'city' => $city,
                'district' => $district,
            ];
        }

        return $rows;
    }

    /**
     * @return array{0:string,1:string}
     */
    private function parseLine(string $line): array
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $line) ?? '');
        if ($normalized === '') {
            return ['', ''];
        }

        $parts = null;
        foreach (['|', '/', '>'] as $delimiter) {
            if (str_contains($normalized, $delimiter)) {
                $parts = explode($delimiter, $normalized, 2);
                break;
            }
        }

        if ($parts === null) {
            return [$normalized, ''];
        }

        $city = trim((string) ($parts[0] ?? ''));
        $district = trim((string) ($parts[1] ?? ''));
        return [$city, $district];
    }

    /**
     * @param array<int, array{city:string,district:string}> $areas
     * @return array<int, array{city_id:?int,district_id:?int,city:string,district:string}>
     */
    private function attachLocationIds(array $areas): array
    {
        if ($areas === [] || !City::query()->exists()) {
            return array_map(static fn(array $row): array => [
                'city_id' => null,
                'district_id' => null,
                'city' => $row['city'],
                'district' => $row['district'],
            ], $areas);
        }

        $citiesByKey = City::query()
            ->select(['id', 'name'])
            ->get()
            ->keyBy(fn(City $city): string => mb_strtolower(trim((string) $city->name)));

        $districtByCity = District::query()
            ->select(['id', 'city_id', 'name'])
            ->get()
            ->groupBy('city_id')
            ->map(fn($rows) => $rows->keyBy(fn(District $district): string => mb_strtolower(trim((string) $district->name))));

        $normalized = [];
        $seen = [];
        foreach ($areas as $row) {
            $cityName = trim((string) ($row['city'] ?? ''));
            $districtName = trim((string) ($row['district'] ?? ''));
            if ($cityName === '') {
                continue;
            }

            $city = $citiesByKey->get(mb_strtolower($cityName));
            $cityId = $city ? (int) $city->id : null;
            if ($city) {
                $cityName = (string) $city->name;
            }

            $districtId = null;
            if ($districtName !== '' && $cityId !== null) {
                $district = $districtByCity->get($cityId)?->get(mb_strtolower($districtName));
                if ($district) {
                    $districtId = (int) $district->id;
                    $districtName = (string) $district->name;
                }
            }

            $dedupe = ($cityId !== null ? ('c:' . $cityId) : ('cs:' . mb_strtolower($cityName)))
                . '|'
                . ($districtId !== null ? ('d:' . $districtId) : ('ds:' . mb_strtolower($districtName)));
            if (isset($seen[$dedupe])) {
                continue;
            }
            $seen[$dedupe] = true;

            $normalized[] = [
                'city_id' => $cityId,
                'district_id' => $districtId,
                'city' => $cityName,
                'district' => $districtName,
            ];
        }

        return $normalized;
    }
}
