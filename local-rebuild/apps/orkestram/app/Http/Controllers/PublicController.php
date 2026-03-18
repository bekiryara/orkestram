<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\CityPage;
use App\Models\District;
use App\Models\Listing;
use App\Models\ListingAttributeValue;
use App\Models\ListingFeedback;
use App\Models\ListingServiceArea;
use App\Models\Page;
use App\Services\Listings\ListingFilterOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(Request $request): View
    {
        $site = $this->siteFromRequest($request);

        $heroPage = Page::query()
            ->where('site', $site)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        $featuredListings = Listing::query()
            ->with([
                'category:id,name,slug',
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();
        $cardAttributesByListing = [];
        foreach ($featuredListings as $listing) {
            if (!$listing instanceof Listing) {
                continue;
            }
            $cardAttributesByListing[(int) $listing->id] = $this->visibleAttributesForListing($listing, 'card');
        }

        $featuredCities = CityPage::query()
            ->where('site', $site)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/';
        $siteMeta = $this->siteMeta($site);
        return view('frontend.home', compact('site', 'siteMeta', 'heroPage', 'featuredListings', 'featuredCities', 'cardAttributesByListing', 'canonicalUrl'));
    }

    public function listings(Request $request): View
    {
        $site = $this->siteFromRequest($request);

        $baseQuery = Listing::query()
            ->with([
                'category:id,name,slug',
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('status', 'published');

        $cityId = max(0, (int) $request->query('city_id', 0));
        $districtId = max(0, (int) $request->query('district_id', 0));
        $city = trim((string) $request->query('city'));
        $district = trim((string) $request->query('district'));

        if ($cityId <= 0 && $city !== '' && City::query()->exists()) {
            $resolvedCity = City::query()->where('name', $city)->first();
            $cityId = $resolvedCity ? (int) $resolvedCity->id : 0;
        }

        if ($cityId > 0) {
            $cityRow = City::query()->find($cityId);
            if ($cityRow) {
                $city = (string) $cityRow->name;
            } else {
                $city = '';
                $district = '';
                $cityId = 0;
                $districtId = 0;
            }
        }

        if ($districtId <= 0 && $district !== '' && $cityId > 0 && District::query()->exists()) {
            $resolvedDistrict = District::query()
                ->where('city_id', $cityId)
                ->where('name', $district)
                ->first();
            $districtId = $resolvedDistrict ? (int) $resolvedDistrict->id : 0;
        }

        if ($districtId > 0) {
            $districtRow = District::query()->where('city_id', $cityId)->find($districtId);
            if ($districtRow) {
                $district = (string) $districtRow->name;
            } else {
                $district = '';
                $districtId = 0;
            }
        }

        $categorySlug = trim((string) $request->query('category'));
        $search = trim((string) $request->query('q'));
        $sort = trim((string) $request->query('sort', 'recommended'));
        [$priceMin, $priceMax] = $this->readPriceRange($request);

        if ($city !== '') {
            $baseQuery->matchCoverage($city, $district, $cityId > 0 ? $cityId : null, $districtId > 0 ? $districtId : null);
        }
        if ($categorySlug !== '') {
            $baseQuery->whereHas('category', fn($q) => $q->where('slug', $categorySlug)->where('is_active', true));
        }
        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $dynamicFilters = [];
        if ($categorySlug !== '') {
            $selectedCategory = Category::query()
                ->where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();

            if ($selectedCategory) {
                $attributes = CategoryAttribute::query()
                    ->where('category_id', (int) $selectedCategory->id)
                    ->where('is_active', true)
                    ->where('is_filterable', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
                $resolvedFilterData = app(ListingFilterOptionService::class)
                    ->buildForAttributes($site, (int) $selectedCategory->id, $attributes);

                foreach ($attributes as $attribute) {
                    $resolved = $resolvedFilterData[(int) $attribute->id] ?? ['options' => [], 'min' => null, 'max' => null];
                    $queryKey = 'attr_' . $attribute->key;
                    $fieldType = (string) $attribute->field_type;
                    $rawQueryValue = $request->query($queryKey, '');
                    $rawValue = is_array($rawQueryValue) ? '' : trim((string) $rawQueryValue);
                    $rawValues = [];
                    if ($fieldType === 'multiselect') {
                        $rawQueryValues = $request->query($queryKey, []);
                        if (!is_array($rawQueryValues)) {
                            $rawQueryValues = $rawQueryValues === '' ? [] : explode(',', (string) $rawQueryValues);
                        }
                        $rawValues = array_values(array_filter(array_map(
                            static fn ($v): string => trim((string) $v),
                            $rawQueryValues
                        ), static fn (string $v): bool => $v !== ''));
                    }
                    $minValue = trim((string) $request->query($queryKey . '_min', ''));
                    $maxValue = trim((string) $request->query($queryKey . '_max', ''));
                    $normalizedValue = mb_strtolower($rawValue);
                    $normalizedValues = array_map(static fn (string $v): string => mb_strtolower($v), $rawValues);
                    $filterMode = (string) ($attribute->filter_mode ?: 'exact');

                    if ($filterMode === 'range' && $fieldType === 'number') {
                        if ($minValue !== '' || $maxValue !== '') {
                            $baseQuery->whereHas('attributeValues', function ($q) use ($attribute, $minValue, $maxValue) {
                                $q->where('category_attribute_id', (int) $attribute->id);
                                if ($minValue !== '') {
                                    $q->where('value_number', '>=', (float) $minValue);
                                }
                                if ($maxValue !== '') {
                                    $q->where('value_number', '<=', (float) $maxValue);
                                }
                            });
                        }
                    } elseif ($fieldType === 'multiselect' && $normalizedValues !== []) {
                        $baseQuery->whereHas('attributeValues', function ($q) use ($attribute, $normalizedValues) {
                            $q->where('category_attribute_id', (int) $attribute->id)
                                ->where(function ($sq) use ($normalizedValues) {
                                    foreach ($normalizedValues as $normalizedOption) {
                                        $sq->orWhereJsonContains('value_json', $normalizedOption);
                                    }
                                });
                        });
                    } elseif ($rawValue !== '') {
                        $baseQuery->whereHas('attributeValues', function ($q) use ($attribute, $normalizedValue) {
                            $q->where('category_attribute_id', (int) $attribute->id)
                                ->where('normalized_value', $normalizedValue);
                        });
                    }

                    $dynamicFilters[] = [
                        'query_key' => $queryKey,
                        'label' => (string) $attribute->label,
                        'field_type' => $fieldType,
                        'filter_mode' => $filterMode,
                        'value' => $rawValue,
                        'values' => $rawValues,
                        'min' => $minValue,
                        'max' => $maxValue,
                        'options' => !empty($resolved['options'])
                            ? $resolved['options']
                            : (is_array($attribute->options_json) ? $attribute->options_json : []),
                        'resolved_min' => $resolved['min'],
                        'resolved_max' => $resolved['max'],
                    ];
                }
            }
        }

        $allowedSorts = ['recommended', 'newest', 'oldest', 'name_asc', 'name_desc', 'price_asc', 'price_desc'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'recommended';
        }

        $requiresPricePipeline = $priceMin !== null || $priceMax !== null || in_array($sort, ['price_asc', 'price_desc'], true);

        if ($requiresPricePipeline) {
            $rawRows = $baseQuery->orderByDesc('published_at')->orderByDesc('id')->get();
            $sortedRows = $this->applyListingSortAndPriceFilter($rawRows, $sort, $priceMin, $priceMax);
            $items = $this->paginateCollection($sortedRows, 9, $request);
        } else {
            switch ($sort) {
                case 'name_asc':
                    $baseQuery->orderBy('name');
                    break;
                case 'name_desc':
                    $baseQuery->orderByDesc('name');
                    break;
                case 'oldest':
                    $baseQuery->orderBy('published_at')->orderBy('id');
                    break;
                case 'newest':
                    $baseQuery->orderByDesc('published_at')->orderByDesc('id');
                    break;
                default:
                    $sort = 'recommended';
                    $baseQuery->orderByDesc('published_at')->orderByDesc('id');
                    break;
            }

            /** @var LengthAwarePaginator $items */
            $items = $baseQuery->paginate(9)->withQueryString();
        }
        $cardAttributesByListing = [];
        foreach ($items->items() as $listing) {
            if (!$listing instanceof Listing) {
                continue;
            }
            $cardAttributesByListing[(int) $listing->id] = $this->visibleAttributesForListing($listing, 'card');
        }

        $cities = [];
        $districts = [];
        $districtMap = [];
        $publishedForOptions = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->with('serviceAreas:id,listing_id,city,district')
            ->get(['id', 'city', 'district']);

        $serviceAreaCities = $publishedForOptions
            ->flatMap(fn(Listing $listing) => $listing->serviceAreas->pluck('city'));
        $cityNames = $this->sortedUnique($publishedForOptions->pluck('city')->merge($serviceAreaCities));
        $cities = collect($cityNames)->map(fn(string $name): array => ['id' => 0, 'name' => $name])->all();

        $serviceAreaDistricts = $publishedForOptions
            ->flatMap(function (Listing $listing) use ($city) {
                return $listing->serviceAreas
                    ->filter(fn($row) => $city === '' || (string) $row->city === $city)
                    ->pluck('district');
            });
        $primaryDistricts = ($city === '')
            ? $publishedForOptions->pluck('district')
            : $publishedForOptions->where('city', $city)->pluck('district');
        $districtNames = $this->sortedUnique($primaryDistricts->merge($serviceAreaDistricts));
        $districts = collect($districtNames)->map(fn(string $name): array => ['id' => 0, 'name' => $name])->all();
        $districtMap = $this->buildStringDistrictMapFromListings($publishedForOptions);

        $categoryOptions = Category::query()
            ->with('mainCategory:id,name,sort_order')
            ->where('is_active', true)
            ->whereHas('listings', function ($q) use ($site) {
                $q->visibleForSite($site)->where('status', 'published');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['main_category_id', 'name', 'slug', 'sort_order']);

        $categoryGroups = $categoryOptions
            ->sortBy(function ($item) {
                $mainOrder = $item->mainCategory?->sort_order ?? 999999;
                $mainName = mb_strtolower((string) ($item->mainCategory?->name ?? 'Diger'));
                $leafOrder = $item->sort_order ?? 999999;
                $leafName = mb_strtolower((string) $item->name);

                return sprintf('%06d|%s|%06d|%s', $mainOrder, $mainName, $leafOrder, $leafName);
            })
            ->groupBy(function ($item) {
                return $item->mainCategory?->name ?? 'Diger';
            });

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/ilanlar';
        $siteMeta = $this->siteMeta($site);

        return view('frontend.listings', compact(
            'site',
            'siteMeta',
            'items',
            'cityId',
            'districtId',
            'city',
            'district',
            'categorySlug',
            'search',
            'sort',
            'priceMin',
            'priceMax',
            'cities',
            'districts',
            'districtMap',
            'dynamicFilters',
            'cardAttributesByListing',
            'categoryGroups',
            'canonicalUrl'
        ));
    }

    public function listing(Request $request, string $slug): View
    {
        $site = $this->siteFromRequest($request);

        $item = Listing::query()
            ->with([
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $relatedListings = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('id', '<>', $item->id)
            ->where(function ($q) use ($item) {
                if (!empty($item->city)) {
                    $q->orWhere('city', $item->city);
                }
                if (!empty($item->service_type)) {
                    $q->orWhere('service_type', $item->service_type);
                }
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $publicComments = ListingFeedback::query()
            ->where('site', $site)
            ->where('listing_id', $item->id)
            ->where('kind', 'comment')
            ->where('visibility', 'public')
            ->where('status', 'approved')
            ->with('user:id,name')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/ilan/' . ltrim($item->slug, '/');
        $siteMeta = $this->siteMeta($site);
        $detailAttributes = $this->visibleAttributesForListing($item, 'detail');
        return view('frontend.listing', compact('site', 'siteMeta', 'item', 'relatedListings', 'publicComments', 'canonicalUrl', 'detailAttributes'));
    }

    public function serviceCategory(Request $request, string $slug): View
    {
        $site = $this->siteFromRequest($request);
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        [$priceMin, $priceMax] = $this->readPriceRange($request);

        $itemsQuery = Listing::query()
            ->with([
                'category:id,name,slug',
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->matchCoverage(
                trim((string) $request->query('city')),
                trim((string) $request->query('district')),
                (int) $request->query('city_id', 0),
                (int) $request->query('district_id', 0)
            )
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($priceMin !== null || $priceMax !== null) {
            $rows = $itemsQuery->get();
            $filteredRows = $this->applyListingSortAndPriceFilter($rows, 'recommended', $priceMin, $priceMax);
            $items = $this->paginateCollection($filteredRows, 12, $request);
        } else {
            $items = $itemsQuery->paginate(12)->withQueryString();
        }
        $cardAttributesByListing = [];
        foreach ($items->items() as $listing) {
            if (!$listing instanceof Listing) {
                continue;
            }
            $cardAttributesByListing[(int) $listing->id] = $this->visibleAttributesForListing($listing, 'card');
        }

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/hizmet/' . ltrim($category->slug, '/');
        $siteMeta = $this->siteMeta($site);
        $isEmpty = $items->total() === 0;
        $metaRobots = ($isEmpty || !$category->is_indexable) ? 'noindex, follow' : 'index, follow';

        return view('frontend.service-category', compact(
            'site',
            'siteMeta',
            'category',
            'items',
            'cardAttributesByListing',
            'canonicalUrl',
            'metaRobots',
            'isEmpty',
            'priceMin',
            'priceMax'
        ));
    }

    public function serviceCategoryCity(Request $request, string $slug, string $city): View|RedirectResponse
    {
        $site = $this->siteFromRequest($request);
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $resolvedCity = $this->resolveCategoryCitySlug($site, $category->id, $city);
        if ($resolvedCity === null) {
            abort(404);
        }
        $canonicalCitySlug = Str::slug($resolvedCity);
        $resolvedCityId = (int) (City::query()->where('name', $resolvedCity)->value('id') ?? 0);
        if ($canonicalCitySlug !== trim($city)) {
            return redirect()->route('service-category.city', ['slug' => $category->slug, 'city' => $canonicalCitySlug], 301);
        }

        [$priceMin, $priceMax] = $this->readPriceRange($request);

        $itemsQuery = Listing::query()
            ->with([
                'category:id,name,slug',
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->matchCoverage($resolvedCity, null, $resolvedCityId > 0 ? $resolvedCityId : null, null)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($priceMin !== null || $priceMax !== null) {
            $rows = $itemsQuery->get();
            $filteredRows = $this->applyListingSortAndPriceFilter($rows, 'recommended', $priceMin, $priceMax);
            $items = $this->paginateCollection($filteredRows, 12, $request);
        } else {
            $items = $itemsQuery->paginate(12)->withQueryString();
        }
        $cardAttributesByListing = [];
        foreach ($items->items() as $listing) {
            if (!$listing instanceof Listing) {
                continue;
            }
            $cardAttributesByListing[(int) $listing->id] = $this->visibleAttributesForListing($listing, 'card');
        }

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/hizmet/' . ltrim($category->slug, '/') . '/' . $canonicalCitySlug;
        $siteMeta = $this->siteMeta($site);
        $isEmpty = $items->total() === 0;
        $metaRobots = ($isEmpty || !$category->is_indexable) ? 'noindex, follow' : 'index, follow';

        return view('frontend.service-category', compact(
            'site',
            'siteMeta',
            'category',
            'items',
            'cardAttributesByListing',
            'canonicalUrl',
            'metaRobots',
            'isEmpty',
            'priceMin',
            'priceMax'
        ));
    }

    public function serviceCategoryCityDistrict(Request $request, string $slug, string $city, string $district): View|RedirectResponse
    {
        $site = $this->siteFromRequest($request);
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $resolvedCity = $this->resolveCategoryCitySlug($site, $category->id, $city);
        if ($resolvedCity === null) {
            abort(404);
        }
        $canonicalCitySlug = Str::slug($resolvedCity);
        $resolvedCityId = (int) (City::query()->where('name', $resolvedCity)->value('id') ?? 0);

        $resolvedDistrict = $this->resolveCategoryDistrictSlug($site, $category->id, $resolvedCity, $district);
        if ($resolvedDistrict === null) {
            abort(404);
        }
        $resolvedDistrictId = 0;
        if ($resolvedCityId > 0) {
            $resolvedDistrictId = (int) (District::query()
                ->where('city_id', $resolvedCityId)
                ->where('name', $resolvedDistrict)
                ->value('id') ?? 0);
        }
        $canonicalDistrictSlug = Str::slug($resolvedDistrict);
        if ($canonicalCitySlug !== trim($city) || $canonicalDistrictSlug !== trim($district)) {
            return redirect()->route('service-category.city-district', [
                'slug' => $category->slug,
                'city' => $canonicalCitySlug,
                'district' => $canonicalDistrictSlug,
            ], 301);
        }

        [$priceMin, $priceMax] = $this->readPriceRange($request);

        $itemsQuery = Listing::query()
            ->with([
                'category:id,name,slug',
                'attributeValues.attribute:id,label,field_type,is_active,is_visible_in_card,is_visible_in_detail',
            ])
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->matchCoverage(
                $resolvedCity,
                $resolvedDistrict,
                $resolvedCityId > 0 ? $resolvedCityId : null,
                $resolvedDistrictId > 0 ? $resolvedDistrictId : null
            )
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($priceMin !== null || $priceMax !== null) {
            $rows = $itemsQuery->get();
            $filteredRows = $this->applyListingSortAndPriceFilter($rows, 'recommended', $priceMin, $priceMax);
            $items = $this->paginateCollection($filteredRows, 12, $request);
        } else {
            $items = $itemsQuery->paginate(12)->withQueryString();
        }
        $cardAttributesByListing = [];
        foreach ($items->items() as $listing) {
            if (!$listing instanceof Listing) {
                continue;
            }
            $cardAttributesByListing[(int) $listing->id] = $this->visibleAttributesForListing($listing, 'card');
        }

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/hizmet/' . ltrim($category->slug, '/') . '/' . $canonicalCitySlug . '/' . $canonicalDistrictSlug;
        $siteMeta = $this->siteMeta($site);
        $isEmpty = $items->total() === 0;
        $metaRobots = ($isEmpty || !$category->is_indexable) ? 'noindex, follow' : 'index, follow';

        return view('frontend.service-category', compact(
            'site',
            'siteMeta',
            'category',
            'items',
            'cardAttributesByListing',
            'canonicalUrl',
            'metaRobots',
            'isEmpty',
            'priceMin',
            'priceMax'
        ));
    }

    public function cityPage(Request $request, string $slug): View
    {
        $site = $this->siteFromRequest($request);

        $item = CityPage::query()
            ->where('site', $site)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $cityListings = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('city', $item->city)
            ->when(!empty($item->district), fn($q) => $q->where('district', $item->district))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(9)
            ->get();

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/sehir/' . ltrim($item->slug, '/');
        $siteMeta = $this->siteMeta($site);
        return view('frontend.city-page', compact('site', 'siteMeta', 'item', 'cityListings', 'canonicalUrl'));
    }

    public function page(Request $request, string $slug): View
    {
        $site = $this->siteFromRequest($request);

        $item = Page::query()
            ->where('site', $site)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $canonicalUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/sayfa/' . ltrim($item->slug, '/');
        $siteMeta = $this->siteMeta($site);
        return view('frontend.page', compact('site', 'siteMeta', 'item', 'canonicalUrl'));
    }

    private function siteFromRequest(Request $request): string
    {
        $host = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());

        // Local port mapping for multi-site testing.
        if (str_contains($httpHost, ':8181') || str_contains($httpHost, ':8281')) {
            return 'izmirorkestra.net';
        }

        $host = strtolower($host);
        if (str_contains($host, 'izmirorkestra')) {
            return 'izmirorkestra.net';
        }
        return 'orkestram.net';
    }

    private function siteMeta(string $site): array
    {
        $themes = config('site_themes.sites', []);
        $default = config('site_themes.default', [
            'name' => 'Orkestram',
            'theme' => 'orkestram',
            'tagline' => 'Etkinligine Uygun Muzik Ekibini Bul',
            'lead' => 'Dugun, nisan ve kurumsal etkinlikler icin profesyonel ekipler.',
        ]);

        $siteMeta = $themes[$site] ?? $default;
        if (!is_array($siteMeta)) {
            return $default;
        }

        return array_merge($default, $siteMeta);
    }

    private function sortedUnique(Collection $values): array
    {
        $normalized = [];

        foreach ($values as $value) {
            $name = trim((string) $value);
            if ($name === '') {
                continue;
            }

            $key = Str::of($name)->ascii()->lower()->value();
            if (!array_key_exists($key, $normalized)) {
                $normalized[$key] = $name;
            }
        }

        uasort($normalized, static fn(string $a, string $b): int => strcmp(Str::of($a)->ascii()->lower()->value(), Str::of($b)->ascii()->lower()->value()));

        return array_values($normalized);
    }

    /**
     * @return array<int, array{label:string,value:string}>
     */
    private function visibleAttributesForListing(Listing $listing, string $mode): array
    {
        $rows = [];
        $values = $listing->relationLoaded('attributeValues')
            ? $listing->attributeValues
            : $listing->attributeValues()->with('attribute')->get();

        foreach ($values as $value) {
            if (!$value instanceof ListingAttributeValue) {
                continue;
            }

            $attribute = $value->attribute;
            if (!$attribute || !$attribute->is_active) {
                continue;
            }

            if ($mode === 'card' && !$attribute->is_visible_in_card) {
                continue;
            }
            if ($mode === 'detail' && !$attribute->is_visible_in_detail) {
                continue;
            }

            $formatted = $this->formatAttributeValueForDisplay($value);
            if ($formatted === '') {
                continue;
            }

            $rows[] = [
                'label' => (string) $attribute->label,
                'value' => $formatted,
            ];
        }

        return $rows;
    }

    private function formatAttributeValueForDisplay(ListingAttributeValue $value): string
    {
        $fieldType = (string) ($value->attribute?->field_type ?? '');

        if ($fieldType === 'boolean' && $value->value_bool !== null) {
            return $value->value_bool ? 'Evet' : 'Hayir';
        }

        if ($fieldType === 'number' && $value->value_number !== null) {
            return rtrim(rtrim((string) $value->value_number, '0'), '.');
        }

        if ($fieldType === 'multiselect') {
            $text = trim((string) ($value->value_text ?? ''));
            if ($text !== '') {
                return $text;
            }
            if (is_array($value->value_json) && $value->value_json !== []) {
                return implode(', ', array_map(static fn ($v): string => (string) $v, $value->value_json));
            }
            return '';
        }

        return trim((string) ($value->value_text ?? ''));
    }

    private function resolveCategoryCitySlug(string $site, int $categoryId, string $citySlug): ?string
    {
        $citySlug = trim(mb_strtolower($citySlug));
        if ($citySlug === '') {
            return null;
        }

        $cities = $this->categoryCityOptions($site, $categoryId);
        foreach ($cities as $city) {
            if (Str::slug($city) === $citySlug) {
                return $city;
            }
        }

        return null;
    }

    private function resolveCategoryDistrictSlug(string $site, int $categoryId, string $city, string $districtSlug): ?string
    {
        $districtSlug = trim(mb_strtolower($districtSlug));
        if ($districtSlug === '') {
            return null;
        }

        $districts = $this->categoryDistrictOptions($site, $categoryId, $city);
        foreach ($districts as $district) {
            if (Str::slug($district) === $districtSlug) {
                return $district;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function categoryCityOptions(string $site, int $categoryId): array
    {
        $baseQuery = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $categoryId);

        $locationCities = (clone $baseQuery)
            ->whereIn('coverage_mode', ['location_only', 'hybrid'])
            ->pluck('city');

        $serviceAreaListingIds = (clone $baseQuery)
            ->whereIn('coverage_mode', ['service_area_only', 'hybrid'])
            ->pluck('id');

        $serviceAreaCities = ListingServiceArea::query()
            ->whereIn('listing_id', $serviceAreaListingIds)
            ->pluck('city');

        return $this->sortedUnique($locationCities->merge($serviceAreaCities));
    }

    /**
     * @return array<int, string>
     */
    private function categoryDistrictOptions(string $site, int $categoryId, string $city): array
    {
        $baseQuery = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $categoryId);

        $locationDistricts = (clone $baseQuery)
            ->whereIn('coverage_mode', ['location_only', 'hybrid'])
            ->where('city', $city)
            ->pluck('district');

        $serviceAreaListingIds = (clone $baseQuery)
            ->whereIn('coverage_mode', ['service_area_only', 'hybrid'])
            ->pluck('id');

        $serviceAreaDistricts = ListingServiceArea::query()
            ->whereIn('listing_id', $serviceAreaListingIds)
            ->where('city', $city)
            ->pluck('district');

        return $this->sortedUnique($locationDistricts->merge($serviceAreaDistricts));
    }

    /**
     * @return array<string, array<int, array{id:int,name:string}>>
     */
    private function buildStringDistrictMapFromListings(Collection $publishedForOptions): array
    {
        $map = [];
        foreach ($publishedForOptions as $listing) {
            $city = trim((string) ($listing->city ?? ''));
            $district = trim((string) ($listing->district ?? ''));
            if ($city !== '' && $district !== '') {
                $map[$city][] = ['id' => 0, 'name' => $district];
            }

            foreach ($listing->serviceAreas as $area) {
                $areaCity = trim((string) ($area->city ?? ''));
                $areaDistrict = trim((string) ($area->district ?? ''));
                if ($areaCity !== '' && $areaDistrict !== '') {
                    $map[$areaCity][] = ['id' => 0, 'name' => $areaDistrict];
                }
            }
        }

        foreach ($map as $city => $rows) {
            $unique = collect($rows)
                ->unique(fn(array $row): string => mb_strtolower((string) ($row['name'] ?? '')))
                ->sortBy(fn(array $row): string => mb_strtolower((string) ($row['name'] ?? '')))
                ->values()
                ->all();
            $map[$city] = $unique;
        }

        return $map;
    }

    private function readPriceRange(Request $request): array
    {
        $parse = static function (string $key) use ($request): ?float {
            $raw = trim((string) $request->query($key, ''));
            if ($raw === '') {
                return null;
            }

            $normalized = str_replace(',', '.', $raw);
            if (!is_numeric($normalized)) {
                return null;
            }

            return (float) $normalized;
        };

        $min = $parse('price_min');
        $max = $parse('price_max');

        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [$min, $max];
    }

    private function applyListingSortAndPriceFilter(
        Collection $rows,
        string $sort,
        ?float $priceMin,
        ?float $priceMax
    ): Collection {
        $metaRows = $rows->map(function (Listing $listing): array {
            return [
                'listing' => $listing,
                'price' => $this->numericPriceFromLabel($listing->price_label),
                'published_at' => optional($listing->published_at)->getTimestamp() ?? 0,
                'name' => mb_strtolower((string) $listing->name),
                'id' => (int) $listing->id,
            ];
        });

        if ($priceMin !== null || $priceMax !== null) {
            $metaRows = $metaRows->filter(function (array $row) use ($priceMin, $priceMax): bool {
                $price = $row['price'];
                if ($price === null) {
                    return false;
                }
                if ($priceMin !== null && $price < $priceMin) {
                    return false;
                }
                if ($priceMax !== null && $price > $priceMax) {
                    return false;
                }
                return true;
            })->values();
        }

        $sorted = match ($sort) {
            'name_asc' => $metaRows->sortBy(fn(array $row): string => $row['name']),
            'name_desc' => $metaRows->sortByDesc(fn(array $row): string => $row['name']),
            'oldest' => $metaRows->sortBy(fn(array $row): string => sprintf('%020d-%020d', $row['published_at'], $row['id'])),
            'newest', 'recommended' => $metaRows->sortByDesc(fn(array $row): string => sprintf('%020d-%020d', $row['published_at'], $row['id'])),
            'price_asc' => $metaRows->sortBy(fn(array $row): string => sprintf(
                '%d-%020.6f-%020d',
                $row['price'] === null ? 1 : 0,
                $row['price'] ?? 0.0,
                $row['id']
            )),
            'price_desc' => $metaRows->sortBy(fn(array $row): string => sprintf(
                '%d-%020.6f-%020d',
                $row['price'] === null ? 1 : 0,
                $row['price'] === null ? 0.0 : (999999999999.0 - $row['price']),
                999999999999 - $row['id']
            )),
            default => $metaRows->sortByDesc(fn(array $row): string => sprintf('%020d-%020d', $row['published_at'], $row['id'])),
        };

        return $sorted->values()->map(fn(array $row): Listing => $row['listing']);
    }

    private function numericPriceFromLabel(?string $priceLabel): ?float
    {
        $label = trim((string) $priceLabel);
        if ($label === '') {
            return null;
        }

        if (!preg_match('/\d+(?:[.,]\d+)?/', $label, $matches)) {
            return null;
        }

        $numeric = str_replace(',', '.', (string) $matches[0]);
        return is_numeric($numeric) ? (float) $numeric : null;
    }

    private function paginateCollection(Collection $rows, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $rows->count();
        $items = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return $paginator->withQueryString();
    }
}

