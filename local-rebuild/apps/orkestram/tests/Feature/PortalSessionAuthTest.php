<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalSessionAuthTest extends TestCase
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

    public function test_login_page_is_accessible(): void
    {
        $this->get('/giris')->assertOk();
    }

    public function test_customer_can_register_and_is_redirected_to_panel(): void
    {
        Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);

        $this->post('/kayit', [
            'name' => 'Customer New',
            'username' => 'customer-new',
            'email' => 'customer-new@example.test',
            'password' => 'customer-pass',
            'password_confirmation' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->assertDatabaseHas('users', [
            'username' => 'customer-new',
            'email' => 'customer-new@example.test',
            'is_active' => 1,
        ]);

        $this->get('/hesabim')->assertOk()->assertSee('customer');
    }

    public function test_owner_can_login_with_session_and_access_panel(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);
        $owner = User::create([
            'name' => 'Owner Session',
            'username' => 'owner-session',
            'email' => 'owner-session@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        $this->post('/giris', [
            'username' => 'owner-session',
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/panel')->assertRedirect('/hesabim');
        $this->get('/hesabim')
            ->assertOk()
            ->assertSee('Ilan Yonetimine Gec')
            ->assertSee('Profil')
            ->assertSee('Guvenlik')
            ->assertSee('Genel Bakis');
    }

    public function test_owner_can_login_with_email_and_access_panel(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);
        $owner = User::create([
            'name' => 'Owner Email',
            'username' => 'owner-email',
            'email' => 'owner-email@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        $this->post('/giris', [
            'username' => 'owner-email@example.test',
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/panel')->assertRedirect('/hesabim');
    }

    public function test_logout_clears_session_access(): void
    {
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);
        $owner = User::create([
            'name' => 'Owner Logout',
            'username' => 'owner-logout',
            'email' => 'owner-logout@example.test',
            'password' => Hash::make('owner-pass'),
            'is_active' => true,
        ]);
        $owner->roles()->attach($ownerRole->id);

        $this->post('/giris', [
            'username' => 'owner-logout',
            'password' => 'owner-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/cikis')->assertRedirect('/giris');
        $this->get('/owner')->assertStatus(401);
    }

    public function test_owner_with_dual_role_sees_owner_panel_and_customer_tools(): void
    {
        $customerRole = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);
        $ownerRole = Role::create(['slug' => 'listing_owner', 'name' => 'Listing Owner', 'is_active' => true]);
        $user = User::create([
            'name' => 'Dual Role User',
            'username' => 'dual-role',
            'email' => 'dual-role@example.test',
            'password' => Hash::make('dual-pass'),
            'is_active' => true,
        ]);
        $user->roles()->attach([$customerRole->id, $ownerRole->id]);

        $this->post('/giris', [
            'username' => 'dual-role',
            'password' => 'dual-pass',
        ])->assertRedirect('/hesabim');

        $this->get('/panel')->assertRedirect('/hesabim');
        $this->get('/hesabim?tab=overview')
            ->assertOk()
            ->assertSee('Profil')
            ->assertSee('Genel Bakis');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->from('/giris')->post('/giris', [
            'username' => 'wrong',
            'password' => 'wrong',
        ])->assertRedirect('/giris');

        $this->get('/owner')->assertStatus(401);
    }

    public function test_login_next_query_redirects_customer_to_listing_page(): void
    {
        $customerRole = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);
        $customer = User::create([
            'name' => 'Customer Next',
            'username' => 'customer-next',
            'email' => 'customer-next@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $target = '/ilan/grup-moda';
        $this->post('/giris', [
            'username' => 'customer-next',
            'password' => 'customer-pass',
            'next' => $target,
        ])->assertRedirect($target);
    }
}
