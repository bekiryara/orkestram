<?php

namespace App\Services\Listings;

use App\Models\CategoryAttribute;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ListingAttributeService
{
    public function validateForCategory(Request $request, ?int $categoryId): void
    {
        $categoryId = (int) ($categoryId ?? 0);
        if ($categoryId <= 0) {
            return;
        }

        $attributes = $this->attributesForCategory($categoryId);
        if ($attributes->isEmpty()) {
            return;
        }

        $rules = $this->rulesForAttributes($attributes);
        Validator::make($request->all(), $rules)->validate();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildFormFields(?int $categoryId, ?Listing $listing = null): array
    {
        $categoryId = (int) ($categoryId ?? 0);
        if ($categoryId <= 0) {
            return [];
        }

        $attributes = $this->attributesForCategory($categoryId);
        if ($attributes->isEmpty()) {
            return [];
        }

        $valuesByAttributeId = [];
        if ($listing) {
            $valuesByAttributeId = $listing->attributeValues()
                ->whereIn('category_attribute_id', $attributes->pluck('id')->all())
                ->get()
                ->keyBy('category_attribute_id')
                ->all();
        }

        $rows = [];
        foreach ($attributes as $attribute) {
            $value = $valuesByAttributeId[(int) $attribute->id] ?? null;
            $selectedValues = [];
            if ($value && is_array($value->value_json)) {
                $selectedValues = array_values(array_filter(array_map(
                    static fn ($v): string => trim((string) $v),
                    $value->value_json
                ), static fn (string $v): bool => $v !== ''));
            }

            $numberValue = '';
            if ($value && $value->value_number !== null) {
                $numberValue = rtrim(rtrim((string) $value->value_number, '0'), '.');
            }

            $rows[] = [
                'id' => (int) $attribute->id,
                'key' => (string) $attribute->key,
                'input_name' => 'attr_' . (string) $attribute->key,
                'label' => (string) $attribute->label,
                'field_type' => (string) $attribute->field_type,
                'is_required' => (bool) $attribute->is_required,
                'options' => is_array($attribute->options_json) ? $attribute->options_json : [],
                'value' => match ((string) $attribute->field_type) {
                    'number' => $numberValue,
                    'boolean' => $value && $value->value_bool !== null ? ((bool) $value->value_bool ? '1' : '0') : '',
                    default => $value ? (string) ($value->value_text ?? '') : '',
                },
                'values' => $selectedValues,
            ];
        }

        return $rows;
    }

    public function syncForRequest(Request $request, Listing $listing, ?int $categoryId): void
    {
        $categoryId = (int) ($categoryId ?? 0);
        if ($categoryId <= 0) {
            $listing->attributeValues()->delete();
            return;
        }

        $attributes = $this->attributesForCategory($categoryId);
        $allowedAttributeIds = $attributes->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($attributes->isEmpty()) {
            $listing->attributeValues()->whereIn('category_attribute_id', $allowedAttributeIds)->delete();
            return;
        }

        $rules = $this->rulesForAttributes($attributes);
        $validated = Validator::make($request->all(), $rules)->validate();

        $upsertRows = [];
        $keepAttributeIds = [];
        foreach ($attributes as $attribute) {
            $attributeId = (int) $attribute->id;
            $input = 'attr_' . (string) $attribute->key;
            $fieldType = (string) $attribute->field_type;
            $rawValue = $validated[$input] ?? null;

            if ($fieldType === 'multiselect') {
                $rawValues = is_array($rawValue) ? $rawValue : [];
                $normalized = array_values(array_unique(array_map(
                    static fn ($v): string => mb_strtolower(trim((string) $v)),
                    array_filter($rawValues, static fn ($v): bool => trim((string) $v) !== '')
                )));
                if ($normalized === []) {
                    continue;
                }

                $upsertRows[] = [
                    'listing_id' => (int) $listing->id,
                    'category_attribute_id' => $attributeId,
                    'value_text' => implode(', ', $rawValues),
                    'value_number' => null,
                    'value_bool' => null,
                    'value_json' => $normalized,
                    'normalized_value' => null,
                ];
                $keepAttributeIds[] = $attributeId;
                continue;
            }

            if ($fieldType === 'number') {
                if ($rawValue === null || $rawValue === '') {
                    continue;
                }
                $normalizedNumber = rtrim(rtrim((string) $rawValue, '0'), '.');
                $upsertRows[] = [
                    'listing_id' => (int) $listing->id,
                    'category_attribute_id' => $attributeId,
                    'value_text' => null,
                    'value_number' => (float) $rawValue,
                    'value_bool' => null,
                    'value_json' => null,
                    'normalized_value' => mb_strtolower($normalizedNumber),
                ];
                $keepAttributeIds[] = $attributeId;
                continue;
            }

            if ($fieldType === 'boolean') {
                if ($rawValue === null || $rawValue === '') {
                    continue;
                }
                $boolValue = (string) $rawValue === '1';
                $upsertRows[] = [
                    'listing_id' => (int) $listing->id,
                    'category_attribute_id' => $attributeId,
                    'value_text' => (string) $rawValue,
                    'value_number' => null,
                    'value_bool' => $boolValue,
                    'value_json' => null,
                    'normalized_value' => $boolValue ? '1' : '0',
                ];
                $keepAttributeIds[] = $attributeId;
                continue;
            }

            $textValue = trim((string) ($rawValue ?? ''));
            if ($textValue === '') {
                continue;
            }

            $upsertRows[] = [
                'listing_id' => (int) $listing->id,
                'category_attribute_id' => $attributeId,
                'value_text' => $textValue,
                'value_number' => null,
                'value_bool' => null,
                'value_json' => null,
                'normalized_value' => mb_strtolower($textValue),
            ];
            $keepAttributeIds[] = $attributeId;
        }

        $query = $listing->attributeValues()->whereIn('category_attribute_id', $allowedAttributeIds);
        if ($keepAttributeIds !== []) {
            $query->whereNotIn('category_attribute_id', $keepAttributeIds);
        }
        $query->delete();

        foreach ($upsertRows as $row) {
            $listing->attributeValues()->updateOrCreate(
                ['category_attribute_id' => (int) $row['category_attribute_id']],
                [
                    'value_text' => $row['value_text'],
                    'value_number' => $row['value_number'],
                    'value_bool' => $row['value_bool'],
                    'value_json' => $row['value_json'],
                    'normalized_value' => $row['normalized_value'],
                ]
            );
        }
    }

    /**
     * @return Collection<int, CategoryAttribute>
     */
    private function attributesForCategory(int $categoryId): Collection
    {
        return CategoryAttribute::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param Collection<int, CategoryAttribute> $attributes
     * @return array<string, array<int, mixed>>
     */
    private function rulesForAttributes(Collection $attributes): array
    {
        $rules = [];
        foreach ($attributes as $attribute) {
            $input = 'attr_' . (string) $attribute->key;
            $required = (bool) $attribute->is_required;
            $fieldType = (string) $attribute->field_type;
            $options = is_array($attribute->options_json) ? $attribute->options_json : [];

            if ($fieldType === 'number') {
                $rules[$input] = $required ? ['required', 'numeric'] : ['nullable', 'numeric'];
                continue;
            }

            if ($fieldType === 'boolean') {
                $rules[$input] = $required ? ['required', Rule::in(['0', '1'])] : ['nullable', Rule::in(['0', '1'])];
                continue;
            }

            if ($fieldType === 'multiselect') {
                $rules[$input] = $required ? ['required', 'array', 'min:1'] : ['nullable', 'array'];
                $rules[$input . '.*'] = ['string', Rule::in($options)];
                continue;
            }

            if ($fieldType === 'select') {
                $rules[$input] = $required ? ['required', 'string', Rule::in($options)] : ['nullable', 'string', Rule::in($options)];
                continue;
            }

            $rules[$input] = $required ? ['required', 'string', 'max:500'] : ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }
}
