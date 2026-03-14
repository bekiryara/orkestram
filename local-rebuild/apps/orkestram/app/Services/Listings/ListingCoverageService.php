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
     * @return array<int, array{city:string,district:string,city_id:?int,district_id:?int}>
     */
    private function parseRawText(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        $jsonAreas = $this->parseJsonAreas($raw);
        if ($jsonAreas !== []) {
            return $jsonAreas;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $seen = [];
        $rows = [];

        foreach ($lines as $line) {
            [$city, $district] = $this->parseLine((string) $line);
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
                'city_id' => null,
                'district_id' => null,
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{city:string,district:string,city_id:?int,district_id:?int}>
     */
    private function parseJsonAreas(string $raw): array
    {
        if (!str_starts_with($raw, '[')) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $rows = [];
        $seen = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $city = trim((string) ($item['city'] ?? ''));
            $district = trim((string) ($item['district'] ?? ''));
            $cityId = isset($item['city_id']) && is_numeric($item['city_id']) ? (int) $item['city_id'] : null;
            $districtId = isset($item['district_id']) && is_numeric($item['district_id']) ? (int) $item['district_id'] : null;

            if ($city === '' && $cityId === null) {
                continue;
            }

            $key = ($cityId !== null ? ('c:' . $cityId) : ('cs:' . mb_strtolower($city)))
                . '|'
                . ($districtId !== null ? ('d:' . $districtId) : ('ds:' . mb_strtolower($district)));
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $rows[] = [
                'city' => $city,
                'district' => $district,
                'city_id' => $cityId,
                'district_id' => $districtId,
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
     * @param array<int, array{city:string,district:string,city_id:?int,district_id:?int}> $areas
     * @return array<int, array{city_id:?int,district_id:?int,city:string,district:string}>
     */
    private function attachLocationIds(array $areas): array
    {
        if ($areas === [] || !City::query()->exists()) {
            return array_map(static fn(array $row): array => [
                'city_id' => isset($row['city_id']) && is_numeric($row['city_id']) ? (int) $row['city_id'] : null,
                'district_id' => isset($row['district_id']) && is_numeric($row['district_id']) ? (int) $row['district_id'] : null,
                'city' => (string) ($row['city'] ?? ''),
                'district' => (string) ($row['district'] ?? ''),
            ], $areas);
        }

        $cities = City::query()->select(['id', 'name'])->get();
        $citiesById = $cities->keyBy('id');
        $citiesByKey = $cities->keyBy(fn(City $city): string => mb_strtolower(trim((string) $city->name)));

        $districts = District::query()->select(['id', 'city_id', 'name'])->get();
        $districtByCity = $districts
            ->groupBy('city_id')
            ->map(fn($rows) => $rows->keyBy(fn(District $district): string => mb_strtolower(trim((string) $district->name))));
        $districtById = $districts->keyBy('id');

        $normalized = [];
        $seen = [];
        foreach ($areas as $row) {
            $cityName = trim((string) ($row['city'] ?? ''));
            $districtName = trim((string) ($row['district'] ?? ''));
            $cityId = isset($row['city_id']) && is_numeric($row['city_id']) ? (int) $row['city_id'] : null;
            $districtId = isset($row['district_id']) && is_numeric($row['district_id']) ? (int) $row['district_id'] : null;

            if ($cityId !== null) {
                $city = $citiesById->get($cityId);
                if ($city) {
                    $cityName = (string) $city->name;
                } else {
                    $cityId = null;
                }
            }

            if ($cityId === null && $cityName !== '') {
                $city = $citiesByKey->get(mb_strtolower($cityName));
                if ($city) {
                    $cityId = (int) $city->id;
                    $cityName = (string) $city->name;
                }
            }

            if ($cityId === null && $cityName === '') {
                continue;
            }

            if ($districtId !== null) {
                $district = $districtById->get($districtId);
                if ($district && (int) $district->city_id === (int) $cityId) {
                    $districtName = (string) $district->name;
                } else {
                    $districtId = null;
                }
            }

            if ($districtId === null && $districtName !== '' && $cityId !== null) {
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
