<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CustomerRequest;
use App\Models\District;
use App\Models\Listing;
use App\Models\Neighborhood;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OwnerCustomerDbFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        putenv('ADMIN_ACCOUNTS_JSON=');
        $_ENV['ADMIN_ACCOUNTS_JSON'] = '';
        $_SERVER['ADMIN_ACCOUNTS_JSON'] = '';

        putenv('ADMIN_DB_AUTH_ENABLED=true');
        $_ENV['ADMIN_DB_AUTH_ENABLED'] = 'true';
        $_SERVER['ADMIN_DB_AUTH_ENABLED'] = 'true';
    }

    public function test_owner_listings_page_shows_only_owned_records(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);

        $ownerA = User::create([
            'name' => 'Owner A',
            'username' => 'owner-a',
            'email' => 'owner-a@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $ownerA->roles()->attach($ownerRole->id);

        $ownerB = User::create([
            'name' => 'Owner B',
            'username' => 'owner-b',
            'email' => 'owner-b@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $ownerB->roles()->attach($ownerRole->id);

        Listing::create([
            'site' => 'orkestram.net',
            'owner_user_id' => $ownerA->id,
            'slug' => 'owner-a-listing',
            'name' => 'Owner A Listing',
            'status' => 'published',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'owner_user_id' => $ownerB->id,
            'slug' => 'owner-b-listing',
            'name' => 'Owner B Listing',
            'status' => 'published',
        ]);

        $headers = [
            'PHP_AUTH_USER' => 'owner-a',
            'PHP_AUTH_PW' => 'owner-pass',
        ];

        $response = $this->withServerVariables($headers)->get('/owner/listings');
        $response->assertOk();
        $response->assertSee('Owner A Listing');
        $response->assertDontSee('Owner B Listing');
    }

    public function test_customer_request_is_saved_with_db_user_id_and_simple_pricing_snapshot(): void
    {
        $customerRole = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);

        $customer = User::create([
            'name' => 'Customer User',
            'username' => 'customer-db',
            'email' => 'customer-db@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'owner_user_id' => $customer->id,
            'slug' => 'simple-pricing-listing',
            'name' => 'Simple Pricing Listing',
            'status' => 'published',
            'price_type' => 'hourly',
            'price_min' => 750,
            'price_max' => null,
            'currency' => 'TRY',
            'meta_json' => ['pricing_mode' => Listing::PRICING_MODE_SIMPLE],
        ]);

        $headers = [
            'PHP_AUTH_USER' => 'customer-db',
            'PHP_AUTH_PW' => 'customer-pass',
        ];

        $this->withServerVariables($headers)->postJson('/customer/requests', [
            'listing_slug' => $listing->slug,
            'name' => 'Ali Veli',
            'phone' => '05321112233',
            'message' => 'Teklif istiyorum',
        ])->assertCreated();

        $this->assertDatabaseHas('customer_requests', [
            'user_id' => $customer->id,
            'listing_id' => $listing->id,
            'name' => 'Ali Veli',
            'status' => 'new',
            'pricing_mode' => Listing::PRICING_MODE_SIMPLE,
            'price_type' => 'hourly',
            'price_min' => 750,
            'price_max' => null,
            'currency' => 'TRY',
            'price_label' => '750 TRY / saat',
        ]);

        $this->assertSame(1, CustomerRequest::query()->count());
    }

    public function test_customer_request_snapshot_stays_fixed_after_listing_price_changes(): void
    {
        $customerRole = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);

        $customer = User::create([
            'name' => 'Snapshot User',
            'username' => 'snapshot-user',
            'email' => 'snapshot-user@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'owner_user_id' => $customer->id,
            'slug' => 'snapshot-listing',
            'name' => 'Snapshot Listing',
            'status' => 'published',
            'price_type' => 'range',
            'price_min' => 1000,
            'price_max' => 1500,
            'currency' => 'TRY',
            'meta_json' => ['pricing_mode' => Listing::PRICING_MODE_SIMPLE],
        ]);

        $headers = [
            'PHP_AUTH_USER' => 'snapshot-user',
            'PHP_AUTH_PW' => 'customer-pass',
        ];

        $this->withServerVariables($headers)->postJson('/customer/requests', [
            'listing_slug' => $listing->slug,
            'name' => 'Snapshot Talebi',
            'message' => 'Ilk fiyat kaydi',
        ])->assertCreated();

        $requestRow = CustomerRequest::query()->sole();
        $this->assertSame('1000 - 1500 TRY', $requestRow->price_label);

        $listing->forceFill([
            'price_type' => 'hourly',
            'price_min' => 2200,
            'price_max' => null,
            'currency' => 'USD',
        ])->save();

        $requestRow->refresh();

        $this->assertSame('range', $requestRow->price_type);
        $this->assertSame('1000.00', (string) $requestRow->price_min);
        $this->assertSame('1500.00', (string) $requestRow->price_max);
        $this->assertSame('TRY', $requestRow->currency);
        $this->assertSame('1000 - 1500 TRY', $requestRow->price_label);
    }

    public function test_owner_can_create_listing_from_owner_panel(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);
        $city = City::create(['name' => 'Izmir', 'slug' => 'izmir', 'sort_order' => 1]);
        $district = District::create(['city_id' => $city->id, 'name' => 'Konak', 'slug' => 'konak', 'sort_order' => 1]);
        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'district_id' => $district->id,
            'name' => 'Alsancak',
            'slug' => 'alsancak',
            'sort_order' => 1,
        ]);

        $owner = User::create([
            'name' => 'Owner C',
            'username' => 'owner-c',
            'email' => 'owner-c@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        $headers = [
            'PHP_AUTH_USER' => 'owner-c',
            'PHP_AUTH_PW' => 'owner-pass',
        ];

        $this->withServerVariables($headers)->post('/owner/listings', [
            'name' => 'Yeni Owner Ilani',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'avenue_name' => 'Cumhuriyet',
            'street_name' => '1420',
            'building_no' => '11',
            'unit_no' => '4',
            'service_type' => 'Dugun Muzik',
            'price_type' => 'fixed',
            'price_min' => '15000',
            'currency' => 'TRY',
            'coverage_mode' => 'location_only',
        ])->assertRedirect('/owner/listings');

        $this->assertDatabaseHas('listings', [
            'owner_user_id' => $owner->id,
            'name' => 'Yeni Owner Ilani',
            'site' => 'orkestram.net',
            'status' => 'draft',
        ]);
    }
}

