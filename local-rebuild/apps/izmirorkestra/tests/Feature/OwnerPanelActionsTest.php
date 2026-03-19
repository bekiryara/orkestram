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

class OwnerPanelActionsTest extends TestCase
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

    public function test_owner_can_manage_own_listing_and_lead_only(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Owner', 'is_active' => true]);
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

        $aListing = Listing::create([
            'site' => 'izmirorkestra.net',
            'owner_user_id' => $ownerA->id,
            'slug' => 'owner-a-listing',
            'name' => 'Owner A Listing',
            'status' => 'draft',
        ]);
        $bListing = Listing::create([
            'site' => 'izmirorkestra.net',
            'owner_user_id' => $ownerB->id,
            'slug' => 'owner-b-listing',
            'name' => 'Owner B Listing',
            'status' => 'draft',
        ]);

        $aLead = CustomerRequest::create([
            'site' => 'izmirorkestra.net',
            'listing_id' => $aListing->id,
            'name' => 'Lead A',
            'status' => 'new',
        ]);
        $bLead = CustomerRequest::create([
            'site' => 'izmirorkestra.net',
            'listing_id' => $bListing->id,
            'name' => 'Lead B',
            'status' => 'new',
        ]);

        $this->post('/giris', [
            'username' => 'owner-a',
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/owner/listings/' . $aListing->id . '/status', [
            'status' => 'published',
        ])->assertRedirect();
        $this->assertDatabaseHas('listings', ['id' => $aListing->id, 'status' => 'published']);

        $this->post('/owner/leads/' . $aLead->id . '/status', [
            'status' => 'contacted',
            'internal_note' => 'Arandi',
        ])->assertRedirect();
        $this->assertDatabaseHas('customer_requests', [
            'id' => $aLead->id,
            'status' => 'contacted',
            'internal_note' => 'Arandi',
        ]);

        $this->post('/owner/listings/' . $bListing->id . '/status', [
            'status' => 'published',
        ])->assertForbidden();

        $this->post('/owner/leads/' . $bLead->id . '/status', [
            'status' => 'closed',
        ])->assertForbidden();
    }

    public function test_owner_listing_create_and_update_syncs_coverage_mode_and_service_areas(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Owner', 'is_active' => true]);
        $city = City::create(['name' => 'Izmir', 'slug' => 'izmir', 'sort_order' => 1]);
        $district = District::create(['city_id' => $city->id, 'name' => 'Konak', 'slug' => 'konak', 'sort_order' => 1]);
        $neighborhood = Neighborhood::create([
            'city_id' => $city->id,
            'district_id' => $district->id,
            'name' => 'Alsancak',
            'slug' => 'alsancak',
            'sort_order' => 1,
        ]);

        $serviceCity = City::create(['name' => 'Manisa', 'slug' => 'manisa', 'sort_order' => 2]);
        $serviceDistrict = District::create(['city_id' => $serviceCity->id, 'name' => 'Sehzadeler', 'slug' => 'sehzadeler', 'sort_order' => 1]);

        $owner = User::create([
            'name' => 'Owner Coverage',
            'username' => 'owner-coverage',
            'email' => 'owner-coverage@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        $this->post('/giris', [
            'username' => 'owner-coverage',
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/owner/listings', [
            'name' => 'Coverage Listing',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'avenue_name' => 'Cumhuriyet',
            'street_name' => '1420',
            'building_no' => '11',
            'unit_no' => '4',
            'service_type' => 'Dugun Muzik',
            'coverage_mode' => 'hybrid',
            'service_area_city_ids' => [(string) $serviceCity->id],
            'service_area_district_ids' => [(string) $serviceDistrict->id],
            'service_areas_text' => 'Manisa | Sehzadeler',
        ])->assertRedirect('/owner/listings');

        /** @var Listing $listing */
        $listing = Listing::query()->where('slug', 'coverage-listing')->firstOrFail();
        $listing->load('serviceAreas');

        $this->assertSame('hybrid', $listing->coverage_mode);
        $this->assertCount(1, $listing->serviceAreas);
        $this->assertSame('Manisa', $listing->serviceAreas->first()->city);
        $this->assertSame('Sehzadeler', $listing->serviceAreas->first()->district);

        $this->get('/owner/listings/' . $listing->id . '/edit')
            ->assertOk()
            ->assertSee('name="coverage_mode"', false)
            ->assertSee("data-selected='[\"{$serviceCity->id}\"]'", false)
            ->assertSee("data-selected='[\"{$serviceDistrict->id}\"]'", false);

        $this->put('/owner/listings/' . $listing->id, [
            'name' => 'Coverage Listing Updated',
            'category_id' => '',
            'city_id' => $city->id,
            'district_id' => $district->id,
            'neighborhood_id' => $neighborhood->id,
            'avenue_name' => 'Cumhuriyet',
            'street_name' => '1420',
            'building_no' => '11',
            'unit_no' => '4',
            'address_note' => '',
            'service_type' => 'Dugun Muzik',
            'price_label' => '',
            'coverage_mode' => 'service_area_only',
            'phone' => '',
            'whatsapp' => '',
            'summary' => '',
            'content' => '',
            'service_area_city_ids' => [(string) $serviceCity->id],
            'service_area_district_ids' => [],
            'service_areas_text' => 'Manisa',
        ])->assertRedirect('/owner/listings');

        $listing->refresh()->load('serviceAreas');

        $this->assertSame('service_area_only', $listing->coverage_mode);
        $this->assertCount(1, $listing->serviceAreas);
        $this->assertSame('Manisa', $listing->serviceAreas->first()->city);
        $this->assertSame('', $listing->serviceAreas->first()->district);
    }
}

