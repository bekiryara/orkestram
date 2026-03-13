<?php

namespace App\Http\Requests\Admin;

use App\Models\City;
use App\Models\District;
use App\Models\Listing;
use App\Models\Neighborhood;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('slug')) {
            $this->merge(['slug' => Str::slug((string) $this->input('slug'))]);
        }
    }

    public function rules(): array
    {
        $site = (string) $this->input('site');
        /** @var Listing|null $listing */
        $listing = $this->route('listing');
        $ignoreId = $listing?->id;
        $cityIdRules = ['required', 'integer'];
        $districtIdRules = ['required', 'integer'];
        $neighborhoodIdRules = ['required', 'integer'];
        if (City::query()->exists()) {
            $cityIdRules[] = Rule::exists('cities', 'id');
            $districtIdRules[] = Rule::exists('districts', 'id');
            $neighborhoodIdRules[] = Rule::exists('neighborhoods', 'id');
        }

        return [
            'site' => ['required', 'string', 'max:64'],
            'site_scope' => ['required', 'in:single,both'],
            'coverage_mode' => ['required', 'in:location_only,service_area_only,hybrid'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('listings', 'slug')
                    ->where(fn($q) => $q->where('site', $site))
                    ->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(fn($q) => $q->where('is_active', true))],
            'city_id' => $cityIdRules,
            'district_id' => $districtIdRules,
            'neighborhood_id' => $neighborhoodIdRules,
            'avenue_name' => ['required', 'string', 'max:180'],
            'street_name' => ['required', 'string', 'max:180'],
            'building_no' => ['required', 'string', 'max:40'],
            'unit_no' => ['required', 'string', 'max:40'],
            'address_note' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'price_label' => ['required', 'string', 'max:120'],
            'summary' => ['required', 'string', 'min:30', 'max:500'],
            'content' => ['required', 'string', 'min:80'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:32'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'gallery_images.*' => ['nullable', 'image', 'max:4096'],
            'features_text' => ['nullable', 'string'],
            'remove_cover_image' => ['nullable', 'in:0,1'],
            'reset_gallery' => ['nullable', 'in:0,1'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['nullable', 'string', 'max:255'],
            'gallery_order' => ['nullable', 'string'],
            'service_areas_text' => ['nullable', 'string', 'max:4000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (!City::query()->exists()) {
                return;
            }

            $cityId = (int) $this->input('city_id', 0);
            $districtId = (int) $this->input('district_id', 0);
            $neighborhoodId = (int) $this->input('neighborhood_id', 0);
            if ($cityId <= 0 || $districtId <= 0 || $neighborhoodId <= 0) {
                return;
            }

            $isValidPair = District::query()
                ->where('id', $districtId)
                ->where('city_id', $cityId)
                ->exists();

            if (!$isValidPair) {
                $validator->errors()->add('district_id', 'Secilen ilce secilen ile ait degil.');
            }

            $isValidNeighborhood = Neighborhood::query()
                ->where('id', $neighborhoodId)
                ->where('city_id', $cityId)
                ->where('district_id', $districtId)
                ->exists();

            if (!$isValidNeighborhood) {
                $validator->errors()->add('neighborhood_id', 'Secilen mahalle il/ilce ile uyumlu degil.');
            }
        });
    }
}
