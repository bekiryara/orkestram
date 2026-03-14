<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Listing;
use App\Models\MainCategory;
use App\Services\Listings\ListingAttributeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CategorySystemFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_category_meta_and_listing_filter_behaviour(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $indexed = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Dugun Orkestrasi',
            'slug' => 'dugun-orkestrasi-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $empty = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Bos Kategori',
            'slug' => 'bos-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 20,
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'slug' => 'kategori-test-ilani',
            'name' => 'Kategori Test Ilani',
            'status' => 'published',
            'category_id' => $indexed->id,
            'city' => 'Izmir',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'slug' => 'diger-ilan',
            'name' => 'Diger Ilan',
            'status' => 'published',
            'city' => 'Izmir',
        ]);

        $this->get('/hizmet/' . $indexed->slug)
            ->assertOk()
            ->assertSee('index, follow');

        $this->get('/hizmet/' . $empty->slug)
            ->assertOk()
            ->assertSee('noindex, follow');

        $this->get('/ilanlar?category=' . $indexed->slug)
            ->assertOk()
            ->assertSee('Kategori Test Ilani')
            ->assertDontSee('Diger Ilan');
    }

    public function test_sitemap_includes_only_indexable_non_empty_category_urls(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $indexed = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Sitemap Kategori',
            'slug' => 'sitemap-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $nonIndexed = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Noindex Kategori',
            'slug' => 'sitemap-noindex-test',
            'is_active' => true,
            'is_indexable' => false,
            'sort_order' => 20,
        ]);

        $empty = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Bos Kategori',
            'slug' => 'sitemap-bos-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 30,
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'slug' => 'sitemap-ilani',
            'name' => 'Sitemap Ilani',
            'status' => 'published',
            'category_id' => $indexed->id,
            'city' => 'Izmir',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'slug' => 'sitemap-noindex-ilani',
            'name' => 'Sitemap Noindex Ilani',
            'status' => 'published',
            'category_id' => $nonIndexed->id,
            'city' => 'Izmir',
        ]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('/hizmet/' . $indexed->slug, $content);
        $this->assertStringNotContainsString('/hizmet/' . $nonIndexed->slug, $content);
        $this->assertStringNotContainsString('/hizmet/' . $empty->slug, $content);
    }

    public function test_city_filter_matches_location_and_service_area_by_coverage_mode(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Coverage Kategori',
            'slug' => 'coverage-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $locationOnly = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'coverage-location-only',
            'name' => 'Coverage Location Only',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        $serviceAreaOnly = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'service_area_only',
            'slug' => 'coverage-service-area-only',
            'name' => 'Coverage Service Area Only',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Ankara',
            'district' => 'Cankaya',
        ]);
        $serviceAreaOnly->serviceAreas()->create([
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        $hybrid = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'hybrid',
            'slug' => 'coverage-hybrid',
            'name' => 'Coverage Hybrid',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Bursa',
            'district' => 'Nilufer',
        ]);
        $hybrid->serviceAreas()->create([
            'city' => 'Izmir',
            'district' => 'Bornova',
        ]);

        $this->get('/ilanlar?city=Izmir')
            ->assertOk()
            ->assertSee('Coverage Location Only')
            ->assertSee('Coverage Service Area Only')
            ->assertSee('Coverage Hybrid');

        $this->get('/ilanlar?city=Izmir&district=Konak')
            ->assertOk()
            ->assertSee('Coverage Location Only')
            ->assertSee('Coverage Service Area Only')
            ->assertDontSee('Coverage Hybrid');

        $this->get('/ilanlar?city=Izmir&district=Bornova')
            ->assertOk()
            ->assertDontSee('Coverage Location Only')
            ->assertDontSee('Coverage Service Area Only')
            ->assertSee('Coverage Hybrid');
    }

    public function test_service_category_city_route_is_deterministic(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Sehir Route Kategori',
            'slug' => 'sehir-route-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'service_area_only',
            'slug' => 'sehir-route-ilan',
            'name' => 'Sehir Route Ilan',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Ankara',
        ]);
        $listing->serviceAreas()->create([
            'city' => 'Izmir',
            'district' => 'Karsiyaka',
        ]);

        $this->get('/hizmet/' . $category->slug . '/izmir')
            ->assertOk()
            ->assertSee('Sehir Route Ilan');

        $this->get('/hizmet/' . $category->slug . '/IZMIR')
            ->assertRedirect('/hizmet/' . $category->slug . '/izmir');

        $this->get('/hizmet/' . $category->slug . '/istanbul')
            ->assertNotFound();

        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('/hizmet/' . $category->slug . '/izmir', $sitemap);
    }

    public function test_service_category_city_district_route_is_deterministic(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Ilce Route Kategori',
            'slug' => 'ilce-route-kategori-test',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'hybrid',
            'slug' => 'ilce-route-ilan',
            'name' => 'Ilce Route Ilan',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listing->serviceAreas()->create([
            'city' => 'Izmir',
            'district' => 'Bornova',
        ]);

        $this->get('/hizmet/' . $category->slug . '/izmir/konak')
            ->assertOk()
            ->assertSee('Ilce Route Ilan');

        $this->get('/hizmet/' . $category->slug . '/izmir/bornova')
            ->assertOk()
            ->assertSee('Ilce Route Ilan');

        $this->get('/hizmet/' . $category->slug . '/IZMIR/BORNOVA')
            ->assertRedirect('/hizmet/' . $category->slug . '/izmir/bornova');

        $this->get('/hizmet/' . $category->slug . '/izmir/karsiyaka')
            ->assertNotFound();

        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('/hizmet/' . $category->slug . '/izmir/konak', $sitemap);
        $this->assertStringContainsString('/hizmet/' . $category->slug . '/izmir/bornova', $sitemap);
    }

    public function test_listing_filter_options_are_listing_bound_and_case_insensitive_unique(): void
    {
        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'options-izmir-one',
            'name' => 'Options Izmir One',
            'status' => 'published',
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'options-izmir-two',
            'name' => 'Options Izmir Two',
            'status' => 'published',
            'city' => 'izmir',
            'district' => 'Bornova',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'options-antalya-one',
            'name' => 'Options Antalya One',
            'status' => 'published',
            'city' => 'Antalya',
            'district' => 'Kepez',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'options-ankara-draft',
            'name' => 'Options Ankara Draft',
            'status' => 'draft',
            'city' => 'Ankara',
            'district' => 'Cankaya',
        ]);

        Listing::create([
            'site' => 'izmirorkestra.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'options-other-site',
            'name' => 'Options Other Site',
            'status' => 'published',
            'city' => 'Manisa',
            'district' => 'Yunusemre',
        ]);

        $this->get('/ilanlar')
            ->assertOk()
            ->assertSee('<option value="Antalya"', false)
            ->assertSee('<option value="Izmir"', false)
            ->assertDontSee('<option value="izmir"', false)
            ->assertDontSee('<option value="Ankara"', false)
            ->assertDontSee('<option value="Manisa"', false);

        $this->get('/ilanlar?city=Antalya')
            ->assertOk()
            ->assertSee('<option value="Kepez"', false)
            ->assertDontSee('<option value="Konak"', false)
            ->assertDontSee('<option value="Bornova"', false);
    }

    public function test_category_attribute_schema_can_store_listing_attribute_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Ozellik Test Kategori',
            'slug' => 'ozellik-test-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $attribute = $category->attributes()->create([
            'key' => 'ekip_sayisi',
            'label' => 'Ekip Sayisi',
            'field_type' => 'number',
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => true,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'ozellik-test-ilan',
            'name' => 'Ozellik Test Ilan',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        $listing->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_number' => 8,
            'normalized_value' => '8',
        ]);

        $this->assertDatabaseHas('category_attributes', [
            'category_id' => $category->id,
            'key' => 'ekip_sayisi',
        ]);

        $this->assertDatabaseHas('listing_attribute_values', [
            'listing_id' => $listing->id,
            'category_attribute_id' => $attribute->id,
            'normalized_value' => '8',
        ]);
    }

    public function test_listing_filters_can_use_category_attribute_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Dinamik Filtre Kategori',
            'slug' => 'dinamik-filtre-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $attribute = $category->attributes()->create([
            'key' => 'ekip_sayisi',
            'label' => 'Ekip Sayisi',
            'field_type' => 'number',
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => true,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listingA = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'dinamik-filtre-ilan-a',
            'name' => 'Dinamik Filtre Ilan A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingA->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_number' => 8,
            'normalized_value' => '8',
        ]);

        $listingB = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'dinamik-filtre-ilan-b',
            'name' => 'Dinamik Filtre Ilan B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingB->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_number' => 5,
            'normalized_value' => '5',
        ]);

        $this->get('/ilanlar?category=' . $category->slug)
            ->assertOk()
            ->assertSee('name="attr_ekip_sayisi"', false)
            ->assertSee('Dinamik Filtre Ilan A')
            ->assertSee('Dinamik Filtre Ilan B');

        $this->get('/ilanlar?category=' . $category->slug . '&attr_ekip_sayisi=8')
            ->assertOk()
            ->assertSee('Dinamik Filtre Ilan A')
            ->assertDontSee('Dinamik Filtre Ilan B');
    }

    public function test_listing_filters_can_use_category_attribute_number_ranges(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Range Filtre Kategori',
            'slug' => 'range-filtre-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $attribute = $category->attributes()->create([
            'key' => 'ekip_sayisi',
            'label' => 'Ekip Sayisi',
            'field_type' => 'number',
            'filter_mode' => 'range',
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => true,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listingA = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'range-filtre-ilan-a',
            'name' => 'Range Filtre Ilan A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingA->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_number' => 4,
            'normalized_value' => '4',
        ]);

        $listingB = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'range-filtre-ilan-b',
            'name' => 'Range Filtre Ilan B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingB->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_number' => 8,
            'normalized_value' => '8',
        ]);

        $this->get('/ilanlar?category=' . $category->slug)
            ->assertOk()
            ->assertSee('name="attr_ekip_sayisi_min"', false)
            ->assertSee('name="attr_ekip_sayisi_max"', false);

        $this->get('/ilanlar?category=' . $category->slug . '&attr_ekip_sayisi_min=6')
            ->assertOk()
            ->assertDontSee('Range Filtre Ilan A')
            ->assertSee('Range Filtre Ilan B');

        $this->get('/ilanlar?category=' . $category->slug . '&attr_ekip_sayisi_max=6')
            ->assertOk()
            ->assertSee('Range Filtre Ilan A')
            ->assertDontSee('Range Filtre Ilan B');
    }

    public function test_listings_filter_prefers_location_ids_when_available(): void
    {
        $city = City::create([
            'name' => 'Izmir',
            'slug' => 'izmir',
            'sort_order' => 1,
        ]);
        $district = District::create([
            'city_id' => $city->id,
            'name' => 'Konak',
            'slug' => 'konak',
            'sort_order' => 1,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'service_area_only',
            'slug' => 'id-filter-ilan',
            'name' => 'ID Filter Ilan',
            'status' => 'published',
            'city' => 'Ankara',
        ]);

        $listing->serviceAreas()->create([
            'city_id' => $city->id,
            'district_id' => $district->id,
            'city' => 'izmir',
            'district' => 'konak',
        ]);

        $this->get('/ilanlar?city_id=' . $city->id . '&district_id=' . $district->id)
            ->assertOk()
            ->assertSee('ID Filter Ilan');
    }

    public function test_listing_attribute_service_syncs_and_filters_multiselect_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Bando Test Kategori',
            'slug' => 'bando-test-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $category->attributes()->create([
            'key' => 'enstrumanlar',
            'label' => 'Enstrumanlar',
            'field_type' => 'multiselect',
            'filter_mode' => 'exact',
            'options_json' => ['Davul', 'Trompet', 'Saksafon'],
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listingA = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'bando-test-ilan-a',
            'name' => 'Bando Test Ilan A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        $listingB = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'bando-test-ilan-b',
            'name' => 'Bando Test Ilan B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);

        $service = app(ListingAttributeService::class);

        $requestA = Request::create('/owner/listings', 'POST', [
            'attr_enstrumanlar' => ['Davul', 'Trompet'],
        ]);
        $service->validateForCategory($requestA, (int) $category->id);
        $service->syncForRequest($requestA, $listingA, (int) $category->id);

        $requestB = Request::create('/owner/listings', 'POST', [
            'attr_enstrumanlar' => ['Saksafon'],
        ]);
        $service->validateForCategory($requestB, (int) $category->id);
        $service->syncForRequest($requestB, $listingB, (int) $category->id);

        $this->assertDatabaseHas('listing_attribute_values', [
            'listing_id' => $listingA->id,
            'normalized_value' => null,
        ]);

        $this->get('/ilanlar?category=' . $category->slug . '&attr_enstrumanlar[]=Davul')
            ->assertOk()
            ->assertSee('Bando Test Ilan A')
            ->assertDontSee('Bando Test Ilan B');
    }

    public function test_listing_card_and_detail_show_visibility_bound_attributes(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Gorunurluk Kategori',
            'slug' => 'gorunurluk-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $cardAttribute = $category->attributes()->create([
            'key' => 'card_only',
            'label' => 'Kart Ozellik',
            'field_type' => 'text',
            'filter_mode' => 'exact',
            'is_required' => false,
            'is_filterable' => false,
            'is_visible_in_card' => true,
            'is_visible_in_detail' => false,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $detailAttribute = $category->attributes()->create([
            'key' => 'detail_only',
            'label' => 'Detay Ozellik',
            'field_type' => 'text',
            'filter_mode' => 'exact',
            'is_required' => false,
            'is_filterable' => false,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'gorunurluk-test-ilan',
            'name' => 'Gorunurluk Test Ilan',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'summary' => 'Gorunurluk test ozeti',
            'content' => 'Gorunurluk test icerigi en az seksen karakter olsun diye yeterli uzunlukta yazildi.',
            'price_label' => '1000 TL',
        ]);

        $listing->attributeValues()->create([
            'category_attribute_id' => $cardAttribute->id,
            'value_text' => 'CARD_VALUE_X',
            'normalized_value' => 'card_value_x',
        ]);

        $listing->attributeValues()->create([
            'category_attribute_id' => $detailAttribute->id,
            'value_text' => 'DETAIL_VALUE_Y',
            'normalized_value' => 'detail_value_y',
        ]);

        $this->get('/ilanlar?category=' . $category->slug)
            ->assertOk()
            ->assertSee('CARD_VALUE_X')
            ->assertDontSee('DETAIL_VALUE_Y');

        $this->get('/ilan/' . $listing->slug)
            ->assertOk()
            ->assertSee('DETAIL_VALUE_Y')
            ->assertDontSee('CARD_VALUE_X');
    }

    public function test_select_filter_options_are_bound_to_published_listing_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Select Option Kategori',
            'slug' => 'select-option-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $attribute = $category->attributes()->create([
            'key' => 'solist_cinsiyeti',
            'label' => 'Solist Cinsiyeti',
            'field_type' => 'select',
            'filter_mode' => 'exact',
            'options_json' => ['Kadin', 'Erkek', 'Karma'],
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listingA = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'select-option-ilan-a',
            'name' => 'Select Option Ilan A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingA->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_text' => 'Kadin',
            'normalized_value' => 'kadin',
        ]);

        $listingB = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'select-option-ilan-b',
            'name' => 'Select Option Ilan B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingB->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_text' => 'Erkek',
            'normalized_value' => 'erkek',
        ]);

        $listingDraft = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'select-option-ilan-draft',
            'name' => 'Select Option Ilan Draft',
            'status' => 'draft',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listingDraft->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_text' => 'Karma',
            'normalized_value' => 'karma',
        ]);

        $this->get('/ilanlar?category=' . $category->slug)
            ->assertOk()
            ->assertSee('option value="Kadin"', false)
            ->assertSee('option value="Erkek"', false)
            ->assertDontSee('option value="Karma"', false);

        $this->get('/ilanlar?category=' . $category->slug . '&attr_solist_cinsiyeti=Kadin')
            ->assertOk()
            ->assertSee('option value="Kadin" selected', false);
    }

    public function test_multiselect_filter_options_are_bound_to_published_listing_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Multi Option Kategori',
            'slug' => 'multi-option-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        $attribute = $category->attributes()->create([
            'key' => 'enstrumanlar',
            'label' => 'Enstrumanlar',
            'field_type' => 'multiselect',
            'filter_mode' => 'exact',
            'options_json' => ['Trompet', 'Davul', 'Tuba'],
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'multi-option-ilan',
            'name' => 'Multi Option Ilan',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
        ]);
        $listing->attributeValues()->create([
            'category_attribute_id' => $attribute->id,
            'value_text' => 'Trompet, Davul',
            'value_json' => ['trompet', 'davul'],
            'normalized_value' => null,
        ]);

        $this->get('/ilanlar?category=' . $category->slug)
            ->assertOk()
            ->assertSee('value="Trompet"', false)
            ->assertSee('value="Davul"', false)
            ->assertDontSee('value="Tuba"', false);
    }

    public function test_listing_price_sort_and_range_filter_are_deterministic(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Fiyat Siralama Kategori',
            'slug' => 'fiyat-siralama-kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'fiyat-a',
            'name' => 'Fiyat A 1500',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => '1500 TL',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'fiyat-b',
            'name' => 'Fiyat B 900',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => '900 TL',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'fiyat-c',
            'name' => 'Fiyat C 3200',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => '3200 TL',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'fiyat-d',
            'name' => 'Fiyat D Bos',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => null,
        ]);

        $ascResponse = $this->get('/ilanlar?category=' . $category->slug . '&sort=price_asc')
            ->assertOk()
            ->assertSee('Fiyat B 900')
            ->assertSee('Fiyat A 1500')
            ->assertSee('Fiyat C 3200')
            ->assertSee('Fiyat D Bos')
            ->assertSee('option value="price_asc" selected', false);

        $ascHtml = (string) $ascResponse->getContent();
        $this->assertTrue(
            strpos($ascHtml, 'Fiyat B 900') < strpos($ascHtml, 'Fiyat A 1500')
            && strpos($ascHtml, 'Fiyat A 1500') < strpos($ascHtml, 'Fiyat C 3200')
            && strpos($ascHtml, 'Fiyat C 3200') < strpos($ascHtml, 'Fiyat D Bos')
        );

        $descResponse = $this->get('/ilanlar?category=' . $category->slug . '&sort=price_desc')
            ->assertOk()
            ->assertSee('Fiyat C 3200')
            ->assertSee('Fiyat A 1500')
            ->assertSee('Fiyat B 900')
            ->assertSee('Fiyat D Bos');

        $descHtml = (string) $descResponse->getContent();
        $this->assertTrue(
            strpos($descHtml, 'Fiyat C 3200') < strpos($descHtml, 'Fiyat A 1500')
            && strpos($descHtml, 'Fiyat A 1500') < strpos($descHtml, 'Fiyat B 900')
            && strpos($descHtml, 'Fiyat B 900') < strpos($descHtml, 'Fiyat D Bos')
        );

        $this->get('/ilanlar?category=' . $category->slug . '&price_min=1000&price_max=2000')
            ->assertOk()
            ->assertSee('Fiyat A 1500')
            ->assertDontSee('Fiyat B 900')
            ->assertDontSee('Fiyat C 3200')
            ->assertDontSee('Fiyat D Bos')
            ->assertSee('name="price_min" value="1000"', false)
            ->assertSee('name="price_max" value="2000"', false);
    }

    public function test_service_category_price_range_filter_keeps_query_values(): void
    {
        $main = MainCategory::create([
            'name' => 'Muzik Ekipleri',
            'slug' => 'muzik-ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $category = Category::create([
            'main_category_id' => $main->id,
            'name' => 'Kategori Fiyat Filtre',
            'slug' => 'kategori-fiyat-filtre',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 10,
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'kategori-fiyat-a',
            'name' => 'Kategori Fiyat A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => '1100 TL',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'kategori-fiyat-b',
            'name' => 'Kategori Fiyat B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'price_label' => '3500 TL',
        ]);

        $this->get('/hizmet/' . $category->slug . '?price_min=1000&price_max=2000')
            ->assertOk()
            ->assertSee('Kategori Fiyat A')
            ->assertDontSee('Kategori Fiyat B')
            ->assertSee('name="price_min" value="1000"', false)
            ->assertSee('name="price_max" value="2000"', false);
    }
}
