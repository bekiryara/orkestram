<?php

namespace Tests\Feature;

use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOwnerRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $accounts = [
            ['user' => 'owner', 'pass' => 'owner-pass', 'role' => 'listing_owner'],
            ['user' => 'customer', 'pass' => 'customer-pass', 'role' => 'customer'],
            ['user' => 'support', 'pass' => 'support-pass', 'role' => 'support_agent'],
        ];

        $json = json_encode($accounts);
        putenv('ADMIN_ACCOUNTS_JSON=' . $json);
        $_ENV['ADMIN_ACCOUNTS_JSON'] = $json;
        $_SERVER['ADMIN_ACCOUNTS_JSON'] = $json;
    }

    public function test_listing_owner_access_is_scoped_to_owner_panel(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'owner',
            'PHP_AUTH_PW' => 'owner-pass',
        ];

        $this->withServerVariables($headers)->get('/owner')->assertOk();
        $this->withServerVariables($headers)->get('/owner/listings')->assertOk();
        $this->withServerVariables($headers)->get('/customer')->assertOk();
        $this->withServerVariables($headers)->get('/admin/pages')->assertForbidden();
    }

    public function test_customer_can_view_and_create_requests_only_in_customer_panel(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'customer',
            'PHP_AUTH_PW' => 'customer-pass',
        ];

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'role-access-simple-listing',
            'name' => 'Role Access Listing',
            'status' => 'published',
            'price_type' => 'fixed',
            'price_min' => 5000,
            'currency' => 'TRY',
            'meta_json' => ['pricing_mode' => Listing::PRICING_MODE_SIMPLE],
        ]);

        $this->withServerVariables($headers)->get('/customer')->assertOk();
        $this->withServerVariables($headers)->get('/customer/requests')->assertOk();
        $this->withServerVariables($headers)->postJson('/customer/requests', [
            'listing_slug' => $listing->slug,
            'name' => 'Test Musteri',
            'message' => 'Talep metni',
        ])->assertCreated();
        $this->withServerVariables($headers)->get('/owner')->assertForbidden();
        $this->withServerVariables($headers)->get('/support')->assertForbidden();
    }

    public function test_customer_request_rejects_structured_pricing_listing(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'customer',
            'PHP_AUTH_PW' => 'customer-pass',
        ];

        $listing = Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'structured-listing',
            'name' => 'Structured Listing',
            'status' => 'published',
            'price_type' => 'fixed',
            'price_min' => 9000,
            'currency' => 'TRY',
            'meta_json' => ['pricing_mode' => Listing::PRICING_MODE_STRUCTURED],
        ]);

        $this->withServerVariables($headers)->postJson('/customer/requests', [
            'listing_slug' => $listing->slug,
            'name' => 'Test Musteri',
            'message' => 'Talep metni',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['listing_slug']);
    }

    public function test_support_agent_can_access_support_requests_only(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'support',
            'PHP_AUTH_PW' => 'support-pass',
        ];

        $this->withServerVariables($headers)->get('/support')->assertOk();
        $this->withServerVariables($headers)->get('/support/requests')->assertOk();
        $this->withServerVariables($headers)->get('/customer')->assertForbidden();
        $this->withServerVariables($headers)->get('/admin/listings')->assertForbidden();
    }
}
