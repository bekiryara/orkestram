<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\CustomerRequest;
use App\Models\Listing;
use App\Models\Neighborhood;
use App\Models\ListingLike;
use App\Models\User;
use App\Services\Locations\LocationDictionaryProvider;
use App\Services\Listings\ListingAttributeService;
use App\Services\Listings\ListingMediaService;
use App\Services\Portal\OwnerResourceAccess;
use App\Services\Portal\PortalContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function __construct(
        private readonly PortalContext $context,
        private readonly OwnerResourceAccess $ownerAccess,
        private readonly LocationDictionaryProvider $locationDictionary,
        private readonly ListingAttributeService $attributeService
    ) {
    }

    public function index(Request $request): View
    {
        $listingsQuery = $this->ownerAccess->listingsQuery($request);
        $leadsQuery = $this->ownerAccess->leadsQuery($request);
        $feedbackQuery = $this->ownerAccess->feedbackQuery($request);

        $listingsCount = (clone $listingsQuery)->count();
        $publishedListingsCount = (clone $listingsQuery)->where('status', 'published')->count();
        $draftListingsCount = (clone $listingsQuery)->where('status', 'draft')->count();
        $pausedListingsCount = (clone $listingsQuery)->where('status', 'paused')->count();

        $leadCount = (clone $leadsQuery)->count();
        $newLeadCount = (clone $leadsQuery)->where('status', 'new')->count();
        $contactedLeadCount = (clone $leadsQuery)->where('status', 'contacted')->count();
        $closedLeadCount = (clone $leadsQuery)->where('status', 'closed')->count();

        $feedbackCount = (clone $feedbackQuery)->whereIn('kind', ['message', 'comment'])->count();
        $messageCount = (clone $feedbackQuery)->where('kind', 'message')->count();
        $commentCount = (clone $feedbackQuery)->where('kind', 'comment')->count();
        $likeCount = ListingLike::query()
            ->where('site', $this->context->site($request))
            ->whereHas('listing', fn($q) => $q->where('owner_user_id', $this->context->adminUserId($request)))
            ->count();

        return view('portal.owner.dashboard', compact(
            'listingsCount',
            'publishedListingsCount',
            'draftListingsCount',
            'pausedListingsCount',
            'leadCount',
            'newLeadCount',
            'contactedLeadCount',
            'closedLeadCount',
            'feedbackCount',
            'messageCount',
            'commentCount',
            'likeCount'
        ));
    }

    public function listings(Request $request): View
    {
        $rows = $this->ownerAccess->listingsQuery($request)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('portal.owner.listings', compact('rows'));
    }

    public function create(Request $request): View
    {
        $adminUserId = $this->context->adminUserId($request);
        if ($adminUserId <= 0) {
            abort(403);
        }

        $locationOptions = $this->locationDictionary->options();
        $categoriesByMain = $this->categoriesByMain();
        $selectedCategoryId = (int) old('category_id', (int) $request->query('category_id', 0));
        $dynamicAttributes = $this->attributeService->buildFormFields($selectedCategoryId, null);

        return view('portal.owner.listings-create', compact('locationOptions', 'categoriesByMain', 'dynamicAttributes'));
    }

    public function store(Request $request, ListingMediaService $mediaService): RedirectResponse
    {
        $adminUserId = $this->context->adminUserId($request);
        if ($adminUserId <= 0) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(fn($q) => $q->where('is_active', true))],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'neighborhood_id' => ['required', 'integer', Rule::exists('neighborhoods', 'id')],
            'avenue_name' => ['required', 'string', 'max:180'],
            'street_name' => ['required', 'string', 'max:180'],
            'building_no' => ['required', 'string', 'max:40'],
            'unit_no' => ['required', 'string', 'max:40'],
            'address_note' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'price_label' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:64'],
            'whatsapp' => ['nullable', 'string', 'max:64'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'content' => ['nullable', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        [$cityName, $districtName] = $this->assertValidLocationSelection($data);

        $site = $this->context->site($request);
        $baseSlug = trim((string) ($data['slug'] ?? ''));
        if ($baseSlug === '') {
            $baseSlug = Str::slug((string) $data['name']);
        }
        if ($baseSlug === '') {
            $baseSlug = 'ilan';
        }

        $slug = $this->uniqueSlugForSite($site, $baseSlug);
        $this->attributeService->validateForCategory($request, isset($data['category_id']) ? (int) $data['category_id'] : 0);

        $listingData = [
            'site' => $site,
            'owner_user_id' => $adminUserId,
            'slug' => $slug,
            'name' => $data['name'],
            'status' => 'draft',
            'category_id' => isset($data['category_id']) ? (int) $data['category_id'] : null,
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'],
            'neighborhood_id' => $data['neighborhood_id'],
            'city' => $cityName,
            'district' => $districtName,
            'avenue_name' => trim((string) $data['avenue_name']),
            'street_name' => trim((string) $data['street_name']),
            'building_no' => trim((string) $data['building_no']),
            'unit_no' => trim((string) $data['unit_no']),
            'address_note' => isset($data['address_note']) ? trim((string) $data['address_note']) : null,
            'service_type' => $data['service_type'] ?? null,
            'price_label' => $data['price_label'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
            'site_scope' => 'single',
        ];
        $listingData = $mediaService->apply($request, $listingData);

        $listing = Listing::query()->create($listingData);
        $this->attributeService->syncForRequest($request, $listing, isset($data['category_id']) ? (int) $data['category_id'] : 0);

        return redirect()
            ->route('owner.listings.index')
            ->with('ok', 'Ilan taslak olarak olusturuldu.');
    }

    public function leads(Request $request): View
    {
        $rows = $this->ownerAccess->leadsQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('portal.owner.leads', compact('rows'));
    }

    public function settings(Request $request): View
    {
        $ownerUserId = (int) ($this->context->adminUserId($request) ?? 0);
        $ownerUser = $ownerUserId > 0 ? User::query()->find($ownerUserId) : null;
        $ownedListingCount = $this->ownerAccess->listingsQuery($request)->count();

        return view('portal.owner.settings', compact('ownerUser', 'ownedListingCount'));
    }

    public function editListing(Request $request, Listing $listing): View
    {
        $item = $listing;
        $locationOptions = $this->locationDictionary->options();
        $categoriesByMain = $this->categoriesByMain();
        $selectedCategoryId = (int) old('category_id', (int) $item->category_id);
        $dynamicAttributes = $this->attributeService->buildFormFields($selectedCategoryId, $item);

        return view('portal.owner.listings-edit', compact('item', 'locationOptions', 'categoriesByMain', 'dynamicAttributes'));
    }

    public function updateListing(Request $request, Listing $listing, ListingMediaService $mediaService): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(fn($q) => $q->where('is_active', true))],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'neighborhood_id' => ['required', 'integer', Rule::exists('neighborhoods', 'id')],
            'avenue_name' => ['required', 'string', 'max:180'],
            'street_name' => ['required', 'string', 'max:180'],
            'building_no' => ['required', 'string', 'max:40'],
            'unit_no' => ['required', 'string', 'max:40'],
            'address_note' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'price_label' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:64'],
            'whatsapp' => ['nullable', 'string', 'max:64'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'content' => ['nullable', 'string', 'max:20000'],
            'remove_cover_image' => ['nullable', 'in:1'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:5120'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['nullable', 'string', 'max:255'],
            'gallery_order' => ['nullable', 'string', 'max:10000'],
            'reset_gallery' => ['nullable', 'in:1'],
        ]);

        [$cityName, $districtName] = $this->assertValidLocationSelection($data);
        $this->attributeService->validateForCategory($request, isset($data['category_id']) ? (int) $data['category_id'] : 0);

        $listingData = [
            'slug' => $listing->slug,
            'name' => $data['name'],
            'category_id' => isset($data['category_id']) ? (int) $data['category_id'] : null,
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'],
            'neighborhood_id' => $data['neighborhood_id'],
            'city' => $cityName,
            'district' => $districtName,
            'avenue_name' => trim((string) $data['avenue_name']),
            'street_name' => trim((string) $data['street_name']),
            'building_no' => trim((string) $data['building_no']),
            'unit_no' => trim((string) $data['unit_no']),
            'address_note' => isset($data['address_note']) ? trim((string) $data['address_note']) : null,
            'service_type' => $data['service_type'] ?? null,
            'price_label' => $data['price_label'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
        ];
        $listingData = $mediaService->apply($request, $listingData, $listing);

        $listing->update($listingData);
        $this->attributeService->syncForRequest($request, $listing, isset($data['category_id']) ? (int) $data['category_id'] : 0);

        return redirect()->route('owner.listings.index')->with('ok', 'Ilan guncellendi.');
    }

    public function updateListingStatus(Request $request, Listing $listing): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:draft,published,paused'],
        ]);

        $listing->update(['status' => $data['status']]);

        return back()->with('ok', 'Ilan durumu guncellendi.');
    }

    public function updateLeadStatus(Request $request, CustomerRequest $customerRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,closed'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $customerRequest->update([
            'status' => $data['status'],
            'internal_note' => $data['internal_note'] ?? null,
        ]);

        return back()->with('ok', 'Lead guncellendi.');
    }

    private function uniqueSlugForSite(string $site, string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while (Listing::query()->where('site', $site)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function categoriesByMain()
    {
        return \App\Models\MainCategory::query()
            ->where('is_active', true)
            ->with(['categories' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{0:string,1:string}
     */
    private function assertValidLocationSelection(array $data): array
    {
        if (!City::query()->exists()) {
            throw ValidationException::withMessages([
                'city_id' => 'Lokasyon sozlugu yuklenmeden ilan kaydi yapilamaz.',
            ]);
        }

        $cityId = (int) ($data['city_id'] ?? 0);
        $districtId = (int) ($data['district_id'] ?? 0);
        $neighborhoodId = (int) ($data['neighborhood_id'] ?? 0);

        $city = City::query()->find($cityId);
        $district = District::query()->find($districtId);
        $neighborhood = Neighborhood::query()->find($neighborhoodId);

        if (!$city || !$district || (int) $district->city_id !== (int) $city->id) {
            throw ValidationException::withMessages([
                'district_id' => 'Secilen ilce secilen ile ait degil.',
            ]);
        }

        if (
            !$neighborhood
            || (int) $neighborhood->city_id !== (int) $city->id
            || (int) $neighborhood->district_id !== (int) $district->id
        ) {
            throw ValidationException::withMessages([
                'neighborhood_id' => 'Secilen mahalle il/ilce ile uyumlu degil.',
            ]);
        }

        return [(string) $city->name, (string) $district->name];
    }
}

