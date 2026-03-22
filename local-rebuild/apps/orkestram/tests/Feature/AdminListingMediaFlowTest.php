<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\District;
use App\Models\Listing;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminListingMediaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_listing_with_cover_and_gallery(): void
    {
        Storage::fake('public');
        [$city, $district, $neighborhood] = $this->createLocation();

        $payload = $this->basePayload($city, $district, $neighborhood, [
            'slug' => 'test-medya-ilan',
            'name' => 'Test Medya Ilan',
            'cover_image' => UploadedFile::fake()->create('cover.jpg', 120, 'image/jpeg'),
            'gallery_images' => [
                UploadedFile::fake()->create('g1.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('g2.jpg', 120, 'image/jpeg'),
            ],
        ]);

        $this->withServerVariables($this->adminHeaders())
            ->post('/admin/listings', $payload)
            ->assertRedirect('/admin/listings?site=orkestram.net');

        $listing = Listing::query()->where('slug', 'test-medya-ilan')->first();
        $this->assertNotNull($listing);
        $this->assertStringStartsWith('storage/uploads/listings/test-medya-ilan/', (string) $listing->cover_image_path);
        $this->assertCount(2, (array) $listing->gallery_json);
        $this->assertSame('simple', $listing->pricingMode());
        Storage::disk('public')->assertExists(str_replace('storage/', '', (string) $listing->cover_image_path));
        Storage::disk('public')->assertExists(str_replace('storage/', '', (string) $listing->gallery_json[0]));
    }


    public function test_admin_cannot_update_structured_pricing_listing_via_simple_form(): void
    {
        Storage::fake('public');
        [$city, $district, $neighborhood] = $this->createLocation();

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'structured-admin-test',
            'name' => 'Structured Admin Test',
            'status' => 'draft',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'city' => 'Izmir',
            'district' => 'Bornova',
            'avenue_name' => 'Anadolu',
            'street_name' => '1200',
            'building_no' => '8',
            'unit_no' => '2',
            'service_type' => 'Bando',
            'price_min' => 9000,
            'currency' => 'TRY',
            'price_type' => 'fixed',
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'meta_json' => ['pricing_mode' => 'structured'],
        ]);

        $response = $this->from('/admin/listings/' . $listing->id . '/edit')
            ->withServerVariables($this->adminHeaders())
            ->put('/admin/listings/' . $listing->id, $this->basePayload($city, $district, $neighborhood, [
                'slug' => 'structured-admin-test',
                'name' => 'Structured Admin Test',
                'status' => 'published',
                'price_type' => 'fixed',
                'price_min' => '9500',
                'price_max' => '',
            ]));

        $response->assertRedirect('/admin/listings/' . $listing->id . '/edit');
        $response->assertSessionHasErrors('price_type');
        $this->assertSame('structured', $listing->fresh()->pricingMode());
        $this->assertSame('draft', $listing->fresh()->status);
    }
    public function test_admin_update_migrates_legacy_paths_reorders_gallery_and_deletes_removed_file(): void
    {
        Storage::fake('public');
        [$city, $district, $neighborhood] = $this->createLocation();

        $this->prepareLegacyPublicFile('uploads/listings/sirala-sil-test/cover-old.jpg');
        $this->prepareLegacyPublicFile('uploads/listings/sirala-sil-test/a.jpg');
        $this->prepareLegacyPublicFile('uploads/listings/sirala-sil-test/b.jpg');
        $this->prepareLegacyPublicFile('uploads/listings/sirala-sil-test/c.jpg');

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'sirala-sil-test',
            'name' => 'Sirala Sil Test',
            'status' => 'published',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'city' => 'Izmir',
            'district' => 'Bornova',
            'avenue_name' => 'Anadolu',
            'street_name' => '1200',
            'building_no' => '8',
            'unit_no' => '2',
            'service_type' => 'Bando',
            'price_label' => 'Baslangic',
            'price_min' => 9000,
            'price_max' => 15000,
            'currency' => 'TRY',
            'price_type' => 'range',
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'cover_image_path' => 'uploads/listings/sirala-sil-test/cover-old.jpg',
            'gallery_json' => [
                'uploads/listings/sirala-sil-test/a.jpg',
                'uploads/listings/sirala-sil-test/b.jpg',
                'uploads/listings/sirala-sil-test/c.jpg',
            ],
        ]);

        $payload = $this->basePayload($city, $district, $neighborhood, [
            'slug' => 'sirala-sil-test',
            'name' => 'Sirala Sil Test',
            'price_min' => 10000,
            'price_max' => '',
            'currency' => 'USD',
            'price_type' => 'starting_from',
            'gallery_order' => json_encode([
                'uploads/listings/sirala-sil-test/c.jpg',
                'uploads/listings/sirala-sil-test/a.jpg',
                'uploads/listings/sirala-sil-test/b.jpg',
            ]),
            'remove_gallery' => [
                'uploads/listings/sirala-sil-test/a.jpg',
            ],
        ]);

        $this->withServerVariables($this->adminHeaders())
            ->put('/admin/listings/' . $listing->id, $payload)
            ->assertRedirect('/admin/listings?site=orkestram.net');

        $listing->refresh();
        $this->assertSame('storage/uploads/listings/sirala-sil-test/cover-old.jpg', $listing->cover_image_path);
        $this->assertSame([
            'storage/uploads/listings/sirala-sil-test/c.jpg',
            'storage/uploads/listings/sirala-sil-test/b.jpg',
        ], $listing->gallery_json);
        Storage::disk('public')->assertExists('uploads/listings/sirala-sil-test/cover-old.jpg');
        Storage::disk('public')->assertExists('uploads/listings/sirala-sil-test/b.jpg');
        Storage::disk('public')->assertExists('uploads/listings/sirala-sil-test/c.jpg');
        Storage::disk('public')->assertMissing('uploads/listings/sirala-sil-test/a.jpg');
        $this->assertFileDoesNotExist(public_path('uploads/listings/sirala-sil-test/a.jpg'));
    }

    public function test_admin_replace_cover_and_reset_gallery_delete_old_storage_files(): void
    {
        Storage::fake('public');
        [$city, $district, $neighborhood] = $this->createLocation();

        Storage::disk('public')->put('uploads/listings/degistir-test/cover-old.jpg', 'old-cover');
        Storage::disk('public')->put('uploads/listings/degistir-test/g1.jpg', 'old-g1');
        Storage::disk('public')->put('uploads/listings/degistir-test/g2.jpg', 'old-g2');

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'degistir-test',
            'name' => 'Degistir Test',
            'status' => 'published',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'avenue_name' => 'Cumhuriyet',
            'street_name' => '1400',
            'building_no' => '10',
            'unit_no' => '4',
            'service_type' => 'Bando',
            'price_label' => 'Baslangic',
            'price_min' => 7000,
            'price_max' => 12000,
            'currency' => 'TRY',
            'price_type' => 'range',
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'cover_image_path' => 'storage/uploads/listings/degistir-test/cover-old.jpg',
            'gallery_json' => [
                'storage/uploads/listings/degistir-test/g1.jpg',
                'storage/uploads/listings/degistir-test/g2.jpg',
            ],
        ]);

        $payload = $this->basePayload($city, $district, $neighborhood, [
            'slug' => 'degistir-test',
            'name' => 'Degistir Test',
            'reset_gallery' => '1',
            'cover_image' => UploadedFile::fake()->create('new-cover.jpg', 120, 'image/jpeg'),
            'gallery_images' => [
                UploadedFile::fake()->create('new-g1.jpg', 120, 'image/jpeg'),
            ],
        ]);

        $this->withServerVariables($this->adminHeaders())
            ->put('/admin/listings/' . $listing->id, $payload)
            ->assertRedirect('/admin/listings?site=orkestram.net');

        $listing->refresh();
        $this->assertStringStartsWith('storage/uploads/listings/degistir-test/', (string) $listing->cover_image_path);
        $this->assertCount(1, (array) $listing->gallery_json);
        Storage::disk('public')->assertMissing('uploads/listings/degistir-test/cover-old.jpg');
        Storage::disk('public')->assertMissing('uploads/listings/degistir-test/g1.jpg');
        Storage::disk('public')->assertMissing('uploads/listings/degistir-test/g2.jpg');
        Storage::disk('public')->assertExists(str_replace('storage/', '', (string) $listing->cover_image_path));
        Storage::disk('public')->assertExists(str_replace('storage/', '', (string) $listing->gallery_json[0]));
    }

    private function createLocation(): array
    {
        $city = City::create(['name' => 'Izmir', 'slug' => 'izmir', 'sort_order' => 1]);
        $district = District::create(['city_id' => $city->id, 'name' => 'Bornova', 'slug' => 'bornova', 'sort_order' => 1]);
        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'district_id' => $district->id,
            'name' => 'Kazimdirik',
            'slug' => 'kazimdirik',
            'sort_order' => 1,
        ]);

        return [$city, $district, $neighborhood];
    }

    private function basePayload(City $city, District $district, Neighborhood $neighborhood, array $overrides = []): array
    {
        return array_merge([
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'ilan',
            'name' => 'Ilan',
            'status' => 'published',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'avenue_name' => 'Cumhuriyet',
            'street_name' => '1420',
            'building_no' => '12',
            'unit_no' => '5',
            'service_type' => 'Dugun Orkestrasi',
            'price_label' => 'Premium',
            'price_min' => '12000',
            'price_max' => '18000',
            'currency' => 'TRY',
            'price_type' => 'range',
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'whatsapp' => '+90 532 111 22 33',
            'phone' => '+90 (232) 333 44 55',
            'features_text' => "Canli muzik\n6 kisilik ekip",
            'seo_title' => 'Test SEO Baslik',
            'seo_description' => 'Test SEO Aciklama',
        ], $overrides);
    }

    private function prepareLegacyPublicFile(string $relativePath): void
    {
        $fullPath = public_path($relativePath);
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, 'legacy-file');
    }

    private function adminHeaders(): array
    {
        return [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'change-me',
        ];
    }
}



