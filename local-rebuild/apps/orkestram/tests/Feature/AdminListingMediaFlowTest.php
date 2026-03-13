<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\District;
use App\Models\Listing;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminListingMediaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_listing_with_cover_and_gallery(): void
    {
        $city = City::create(['name' => 'Izmir', 'slug' => 'izmir', 'sort_order' => 1]);
        $district = District::create(['city_id' => $city->id, 'name' => 'Konak', 'slug' => 'konak', 'sort_order' => 1]);
        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'district_id' => $district->id,
            'name' => 'Alsancak',
            'slug' => 'alsancak',
            'sort_order' => 1,
        ]);

        $payload = [
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'test-medya-ilan',
            'name' => 'Test Medya Ilan',
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
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'whatsapp' => '+90 532 111 22 33',
            'phone' => '+90 (232) 333 44 55',
            'features_text' => "Canli muzik\n6 kisilik ekip",
            'seo_title' => 'Test SEO Baslik',
            'seo_description' => 'Test SEO Aciklama',
            'cover_image' => UploadedFile::fake()->create('cover.jpg', 120, 'image/jpeg'),
            'gallery_images' => [
                UploadedFile::fake()->create('g1.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('g2.jpg', 120, 'image/jpeg'),
            ],
        ];

        $headers = $this->adminHeaders();

        $this->withServerVariables($headers)
            ->post('/admin/listings', $payload)
            ->assertRedirect('/admin/listings?site=orkestram.net');

        $listing = Listing::query()
            ->where('site', 'orkestram.net')
            ->where('slug', 'test-medya-ilan')
            ->first();

        $this->assertNotNull($listing);
        $this->assertNotNull($listing->cover_image_path);
        $this->assertIsArray($listing->gallery_json);
        $this->assertCount(2, $listing->gallery_json);
        $this->assertSame('+905321112233', $listing->whatsapp);
        $this->assertSame('+902323334455', $listing->phone);
        $this->assertSame(['Canli muzik', '6 kisilik ekip'], $listing->features_json);
        $this->assertTrue(file_exists(public_path($listing->cover_image_path)));
    }

    public function test_admin_can_update_gallery_order_and_remove_single_item(): void
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
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'gallery_json' => [
                'uploads/listings/sirala-sil-test/a.jpg',
                'uploads/listings/sirala-sil-test/b.jpg',
                'uploads/listings/sirala-sil-test/c.jpg',
            ],
        ]);

        $payload = [
            'site' => 'orkestram.net',
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'slug' => 'sirala-sil-test',
            'name' => 'Sirala Sil Test',
            'status' => 'published',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'avenue_name' => 'Anadolu',
            'street_name' => '1200',
            'building_no' => '8',
            'unit_no' => '2',
            'service_type' => 'Bando',
            'price_label' => 'Baslangic',
            'summary' => 'Bu ozet metni test icin yeterli uzunlukta tutulmustur.',
            'content' => str_repeat('Detay metni. ', 12),
            'gallery_order' => json_encode([
                'uploads/listings/sirala-sil-test/c.jpg',
                'uploads/listings/sirala-sil-test/a.jpg',
                'uploads/listings/sirala-sil-test/b.jpg',
            ]),
            'remove_gallery' => [
                'uploads/listings/sirala-sil-test/a.jpg',
            ],
        ];

        $headers = $this->adminHeaders();

        $this->withServerVariables($headers)
            ->put('/admin/listings/' . $listing->id, $payload)
            ->assertRedirect('/admin/listings?site=orkestram.net');

        $listing->refresh();
        $this->assertSame([
            'uploads/listings/sirala-sil-test/c.jpg',
            'uploads/listings/sirala-sil-test/b.jpg',
        ], $listing->gallery_json);
    }

    private function adminHeaders(): array
    {
        return [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'change-me',
        ];
    }
}
