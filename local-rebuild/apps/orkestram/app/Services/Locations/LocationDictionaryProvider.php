<?php

namespace App\Services\Locations;

use App\Models\City;
use App\Models\District;
use App\Models\Neighborhood;

class LocationDictionaryProvider
{
    /**
     * @return array{
     *   cities:array<int,array{id:int,name:string}>,
     *   district_map:array<int,array<int,array{id:int,name:string}>>,
     *   neighborhood_map:array<int,array<int,array{id:int,name:string}>>
     * }
     */
    public function options(): array
    {
        $cities = City::query()
            ->select(['id', 'name'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (City $city): array => ['id' => (int) $city->id, 'name' => (string) $city->name])
            ->all();

        $districtMap = [];
        District::query()
            ->select(['id', 'city_id', 'name'])
            ->orderBy('city_id')
            ->orderBy('districts.sort_order')
            ->orderBy('districts.name')
            ->get()
            ->each(function ($row) use (&$districtMap): void {
                $cityId = (int) $row->city_id;
                if (!isset($districtMap[$cityId])) {
                    $districtMap[$cityId] = [];
                }
                $districtMap[$cityId][] = [
                    'id' => (int) $row->id,
                    'name' => (string) $row->name,
                ];
            });

        $neighborhoodMap = [];
        Neighborhood::query()
            ->select(['id', 'district_id', 'name'])
            ->orderBy('district_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function ($row) use (&$neighborhoodMap): void {
                $districtId = (int) $row->district_id;
                if (!isset($neighborhoodMap[$districtId])) {
                    $neighborhoodMap[$districtId] = [];
                }
                $neighborhoodMap[$districtId][] = [
                    'id' => (int) $row->id,
                    'name' => (string) $row->name,
                ];
            });

        return [
            'cities' => $cities,
            'district_map' => $districtMap,
            'neighborhood_map' => $neighborhoodMap,
        ];
    }
}
