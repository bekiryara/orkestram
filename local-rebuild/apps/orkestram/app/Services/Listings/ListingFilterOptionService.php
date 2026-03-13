<?php

namespace App\Services\Listings;

use App\Models\CategoryAttribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ListingFilterOptionService
{
    /**
     * @param Collection<int, CategoryAttribute> $attributes
     * @return array<int, array{options:array<int,string>,min:?float,max:?float}>
     */
    public function buildForAttributes(string $site, int $categoryId, Collection $attributes): array
    {
        $result = [];
        if ($categoryId <= 0 || $attributes->isEmpty()) {
            return $result;
        }

        $attributeById = [];
        foreach ($attributes as $attribute) {
            $id = (int) $attribute->id;
            $attributeById[$id] = $attribute;
            $result[$id] = [
                'options' => [],
                'min' => null,
                'max' => null,
            ];
        }

        $attributeIds = array_keys($attributeById);

        $rows = DB::table('listing_attribute_values as lav')
            ->join('listings as l', 'l.id', '=', 'lav.listing_id')
            ->whereIn('lav.category_attribute_id', $attributeIds)
            ->where('l.category_id', $categoryId)
            ->where('l.status', 'published')
            ->where(function ($q) use ($site) {
                $q->where('l.site', $site)->orWhere('l.site_scope', 'both');
            })
            ->select([
                'lav.category_attribute_id',
                'lav.value_text',
                'lav.value_number',
                'lav.value_json',
            ])
            ->get();

        foreach ($rows as $row) {
            $attributeId = (int) ($row->category_attribute_id ?? 0);
            $attribute = $attributeById[$attributeId] ?? null;
            if (!$attribute) {
                continue;
            }

            $fieldType = (string) $attribute->field_type;

            if ($fieldType === 'number') {
                if ($row->value_number !== null && $row->value_number !== '') {
                    $number = (float) $row->value_number;
                    if ($result[$attributeId]['min'] === null || $number < $result[$attributeId]['min']) {
                        $result[$attributeId]['min'] = $number;
                    }
                    if ($result[$attributeId]['max'] === null || $number > $result[$attributeId]['max']) {
                        $result[$attributeId]['max'] = $number;
                    }
                }
                continue;
            }

            if ($fieldType === 'multiselect') {
                $sourceValues = [];
                $text = trim((string) ($row->value_text ?? ''));
                if ($text !== '') {
                    $sourceValues = array_merge($sourceValues, array_map('trim', explode(',', $text)));
                } elseif (!empty($row->value_json)) {
                    $decoded = json_decode((string) $row->value_json, true);
                    if (is_array($decoded)) {
                        $sourceValues = array_merge($sourceValues, array_map(static fn ($v): string => trim((string) $v), $decoded));
                    }
                }

                foreach ($sourceValues as $value) {
                    if ($value === '') {
                        continue;
                    }
                    $this->pushUniqueOption($result[$attributeId]['options'], $value);
                }
                continue;
            }

            $value = trim((string) ($row->value_text ?? ''));
            if ($value !== '') {
                $this->pushUniqueOption($result[$attributeId]['options'], $value);
            }
        }

        foreach ($result as $attributeId => $data) {
            $options = $data['options'];
            usort($options, static fn (string $a, string $b): int => strcasecmp($a, $b));
            $result[$attributeId]['options'] = $options;
        }

        return $result;
    }

    /**
     * @param array<int, string> $options
     */
    private function pushUniqueOption(array &$options, string $value): void
    {
        $normalized = mb_strtolower($value);
        foreach ($options as $existing) {
            if (mb_strtolower($existing) === $normalized) {
                return;
            }
        }
        $options[] = $value;
    }
}
