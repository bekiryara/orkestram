<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\Listing;
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
            'site' => 'orkestram.net',
            'owner_user_id' => $ownerA->id,
            'slug' => 'owner-a-listing',
            'name' => 'Owner A Listing',
            'status' => 'draft',
        ]);
        $bListing = Listing::create([
            'site' => 'orkestram.net',
            'owner_user_id' => $ownerB->id,
            'slug' => 'owner-b-listing',
            'name' => 'Owner B Listing',
            'status' => 'draft',
        ]);

        $aLead = CustomerRequest::create([
            'site' => 'orkestram.net',
            'listing_id' => $aListing->id,
            'name' => 'Lead A',
            'status' => 'new',
        ]);
        $bLead = CustomerRequest::create([
            'site' => 'orkestram.net',
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
}

