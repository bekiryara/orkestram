<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreListingRequest;
use App\Http\Requests\Admin\UpdateListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\MainCategory;
use App\Services\Locations\LocationDictionaryProvider;
use App\Services\Listings\ListingMediaService;
use App\Services\Listings\ListingPayloadBuilder;
use App\Services\Listings\ListingCoverageService;
use App\Services\Listings\ListingAttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function __construct(private readonly LocationDictionaryProvider $locationDictionary)
    {
    }

    public function index(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $rows = Listing::query()
            ->with('category:id,name,slug')
            ->where('site', $site)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.listings.index', compact('rows', 'site'));
    }

    public function create(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $item = new Listing(['site' => $site, 'site_scope' => 'single', 'coverage_mode' => 'location_only', 'status' => 'draft']);
        $categoriesByMain = $this->categoriesByMain();
        $locationOptions = $this->locationDictionary->options();
        $selectedCategoryId = (int) old('category_id', (int) $request->query('category_id', 0));
        $dynamicAttributes = app(ListingAttributeService::class)->buildFormFields($selectedCategoryId, null);

        return view('admin.listings.form', compact('item', 'categoriesByMain', 'locationOptions', 'dynamicAttributes'));
    }

    public function store(
        StoreListingRequest $request,
        ListingPayloadBuilder $payloadBuilder,
        ListingMediaService $mediaService,
        ListingCoverageService $coverageService,
        ListingAttributeService $attributeService
    ): RedirectResponse {
        $data = $payloadBuilder->build($request->validated());
        $attributeService->validateForCategory($request, (int) ($data['category_id'] ?? 0));
        $this->syncServiceTypeFromCategory($data);
        $data = $mediaService->apply($request, $data);
        $data = $this->prepareSimplePricingPayload($data, null, (string) ($data['status'] ?? 'draft'));
        $listing = Listing::create($data);
        $coverageService->syncFromRawText($listing, (string) $request->input('service_areas_text', ''));
        $attributeService->syncForRequest($request, $listing, (int) ($data['category_id'] ?? 0));

        return redirect()
            ->route('admin.listings.index', ['site' => $data['site']])
            ->with('ok', 'Ilan olusturuldu.');
    }

    public function edit(Listing $listing): View
    {
        $item = $listing->load('serviceAreas');
        $categoriesByMain = $this->categoriesByMain();
        $locationOptions = $this->locationDictionary->options();
        $selectedCategoryId = (int) old('category_id', (int) $item->category_id);
        $dynamicAttributes = app(ListingAttributeService::class)->buildFormFields($selectedCategoryId, $item);

        return view('admin.listings.form', compact('item', 'categoriesByMain', 'locationOptions', 'dynamicAttributes'));
    }

    public function update(
        UpdateListingRequest $request,
        Listing $listing,
        ListingPayloadBuilder $payloadBuilder,
        ListingMediaService $mediaService,
        ListingCoverageService $coverageService,
        ListingAttributeService $attributeService
    ): RedirectResponse {
        $data = $payloadBuilder->build($request->validated());
        $attributeService->validateForCategory($request, (int) ($data['category_id'] ?? 0));
        $this->syncServiceTypeFromCategory($data);
        $data = $mediaService->apply($request, $data, $listing);
        $data = $this->prepareSimplePricingPayload($data, $listing, (string) ($data['status'] ?? $listing->status));
        $listing->update($data);
        $coverageService->syncFromRawText($listing, (string) $request->input('service_areas_text', ''));
        $attributeService->syncForRequest($request, $listing, (int) ($data['category_id'] ?? 0));

        return redirect()
            ->route('admin.listings.index', ['site' => $data['site']])
            ->with('ok', 'Ilan guncellendi.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $site = $listing->site;
        $listing->delete();

        return redirect()
            ->route('admin.listings.index', ['site' => $site])
            ->with('ok', 'Ilan silindi.');
    }

    private function categoriesByMain()
    {
        return MainCategory::query()
            ->where('is_active', true)
            ->with(['categories' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function prepareSimplePricingPayload(array $data, ?Listing $listing, string $targetStatus): array
    {
        if ($listing?->usesStructuredPricing()) {
            throw ValidationException::withMessages([
                'price_type' => 'Bu ilanda structured pricing aktif. Simple pricing formu ile ayni ilanda birlikte kullanilamaz.',
            ]);
        }

        $candidate = $listing ?? new Listing();
        $candidate->fill($data);
        $candidate->markSimplePricing();

        if ($targetStatus === 'published' && !$candidate->canPublishWithSimplePricing()) {
            throw ValidationException::withMessages([
                'status' => 'Ilan yayinlanmadan once simple pricing alanlari eksiksiz ve tutarli olmalidir.',
            ]);
        }

        $data['meta_json'] = $candidate->meta_json;

        return $data;
    }
    private function syncServiceTypeFromCategory(array &$data): void
    {
        $categoryId = (int) ($data['category_id'] ?? 0);
        if ($categoryId <= 0) {
            return;
        }

        $category = Category::query()->select(['id', 'name'])->find($categoryId);
        if (!$category) {
            return;
        }

        if (empty($data['service_type'])) {
            $data['service_type'] = $category->name;
        }
    }
}


