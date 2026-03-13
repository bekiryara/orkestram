<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\Listing;
use App\Models\ListingFeedback;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EndToEndRoleJourneyTest extends TestCase
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

    public function test_end_to_end_customer_owner_support_admin_journey(): void
    {
        [$customer, $owner, $support, $admin] = $this->seedActors();

        $listing = Listing::query()->create([
            'site' => 'orkestram.net',
            'owner_user_id' => $owner->id,
            'slug' => 'e2e-owner-listing',
            'name' => 'E2E Owner Listing',
            'status' => 'published',
        ]);

        $this->post('/giris', [
            'username' => $customer->username,
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');
        $this->get('/customer')->assertOk();
        $this->get('/customer/feedbacks')->assertRedirect('/hesabim?tab=comments');

        $this->post('/customer/requests', [
            'listing_slug' => $listing->slug,
            'name' => 'E2E Musteri',
            'phone' => '05000000000',
            'message' => 'E2E talep metni',
        ])->assertRedirect('/customer/requests');

        $this->post('/customer/feedbacks', [
            'listing_slug' => $listing->slug,
            'kind' => 'message',
            'content' => 'E2E mesaj',
        ])->assertRedirect();

        $this->post('/cikis')->assertRedirect('/giris');

        $lead = CustomerRequest::query()->latest('id')->firstOrFail();
        $feedback = ListingFeedback::query()->latest('id')->firstOrFail();

        $this->post('/giris', [
            'username' => $owner->username,
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');
        $this->get('/owner')->assertOk();
        $this->get('/owner/feedbacks')->assertOk();

        $this->post('/owner/feedbacks/' . $feedback->id . '/status', [
            'status' => 'answered',
            'owner_reply' => 'E2E owner yaniti',
        ])->assertRedirect();
        $this->post('/owner/leads/' . $lead->id . '/status', [
            'status' => 'contacted',
            'internal_note' => 'E2E owner lead notu',
        ])->assertRedirect();
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $support->username,
            'password' => 'support-pass',
        ])->assertRedirect('/hesabim');
        $this->get('/support')->assertOk();
        $this->get('/support/requests')->assertOk();
        $this->post('/support/requests/' . $lead->id . '/status', [
            'status' => 'closed',
            'internal_note' => 'E2E support kapatti',
        ])->assertRedirect();
        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => $admin->username,
            'password' => 'admin-pass',
        ])->assertRedirect('/admin/pages');
        $this->get('/admin/pages')->assertOk();
        $this->get('/admin/listings')->assertOk();
        $this->get('/admin/city-pages')->assertOk();

        $this->assertDatabaseHas('customer_requests', [
            'id' => $lead->id,
            'status' => 'closed',
            'internal_note' => 'E2E support kapatti',
        ]);
        $this->assertDatabaseHas('listing_feedback', [
            'id' => $feedback->id,
            'status' => 'answered',
            'owner_reply' => 'E2E owner yaniti',
            'answered_by_user_id' => $owner->id,
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: User, 3: User}
     */
    private function seedActors(): array
    {
        $roles = [
            'customer' => Role::query()->create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]),
            'listing_owner' => Role::query()->create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]),
            'support_agent' => Role::query()->create(['slug' => 'support_agent', 'name' => 'Support Agent', 'is_active' => true]),
            'admin' => Role::query()->create(['slug' => 'admin', 'name' => 'Admin', 'is_active' => true]),
        ];

        $customer = User::query()->create([
            'name' => 'E2E Customer',
            'username' => 'e2e-customer',
            'email' => 'e2e-customer@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($roles['customer']->id);

        $owner = User::query()->create([
            'name' => 'E2E Owner',
            'username' => 'e2e-owner',
            'email' => 'e2e-owner@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($roles['listing_owner']->id);

        $support = User::query()->create([
            'name' => 'E2E Support',
            'username' => 'e2e-support',
            'email' => 'e2e-support@example.test',
            'password' => Hash::make('support-pass'),
            'is_active' => true,
        ]);
        $support->roles()->attach($roles['support_agent']->id);

        $admin = User::query()->create([
            'name' => 'E2E Admin',
            'username' => 'e2e-admin',
            'email' => 'e2e-admin@example.test',
            'password' => Hash::make('admin-pass'),
            'is_active' => true,
        ]);
        $admin->roles()->attach($roles['admin']->id);

        return [$customer, $owner, $support, $admin];
    }
}

