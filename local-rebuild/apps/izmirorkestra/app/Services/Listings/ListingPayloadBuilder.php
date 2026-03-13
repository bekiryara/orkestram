<?php

namespace App\Services\Listings;

use App\Models\City;
use App\Models\District;

class ListingPayloadBuilder
{
    public function build(array $validated): array
    {
        $data = $validated;

        $data['whatsapp'] = $this->normalizePhone($data['whatsapp'] ?? null);
        $data['phone'] = $this->normalizePhone($data['phone'] ?? null);
        $data['features_json'] = $this->parseFeaturesText($data['features_text'] ?? null);
        $data['avenue_name'] = $this->normalizeText($data['avenue_name'] ?? null);
        $data['street_name'] = $this->normalizeText($data['street_name'] ?? null);
        $data['building_no'] = $this->normalizeText($data['building_no'] ?? null);
        $data['unit_no'] = $this->normalizeText($data['unit_no'] ?? null);
        $data['address_note'] = $this->normalizeText($data['address_note'] ?? null);

        [$data['city'], $data['district']] = $this->resolveLocationNames(
            isset($data['city_id']) ? (int) $data['city_id'] : null,
            isset($data['district_id']) ? (int) $data['district_id'] : null
        );

        unset($data['cover_image'], $data['gallery_images'], $data['features_text'], $data['gallery_images.*'], $data['service_areas_text']);
        unset($data['remove_cover_image'], $data['reset_gallery'], $data['remove_gallery'], $data['remove_gallery.*'], $data['gallery_order']);

        return $data;
    }

    private function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $v = trim($value);
        if ($v === '') {
            return null;
        }

        return preg_replace('/[^0-9+]/', '', $v);
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $v = trim($value);
        return $v === '' ? null : $v;
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private function resolveLocationNames(?int $cityId, ?int $districtId): array
    {
        if (($cityId ?? 0) <= 0 || ($districtId ?? 0) <= 0) {
            return [null, null];
        }

        $city = City::query()->find($cityId);
        $district = District::query()->find($districtId);

        if (!$city || !$district || (int) $district->city_id !== (int) $city->id) {
            return [null, null];
        }

        return [(string) $city->name, (string) $district->name];
    }

    private function parseFeaturesText(?string $text): array
    {
        if ($text === null) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $features = [];
        foreach ($lines as $line) {
            $value = trim($line);
            if ($value !== '') {
                $features[] = $value;
            }
        }

        return array_values(array_unique($features));
    }
}
