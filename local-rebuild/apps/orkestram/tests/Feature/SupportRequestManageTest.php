<?php

namespace Tests\Feature;

use App\Models\CustomerRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupportRequestManageTest extends TestCase
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

    public function test_support_can_update_request_and_customer_cannot(): void
    {
        $supportRole = Role::create(['slug' => 'support_agent', 'name' => 'Support', 'is_active' => true]);
        $customerRole = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);

        $support = User::create([
            'name' => 'Support',
            'username' => 'support-user',
            'email' => 'support-user@example.test',
            'password' => Hash::make('support-pass'),
            'is_active' => true,
        ]);
        $support->roles()->attach($supportRole->id);

        $customer = User::create([
            'name' => 'Customer',
            'username' => 'customer-user',
            'email' => 'customer-user@example.test',
            'password' => Hash::make('customer-pass'),
            'is_active' => true,
        ]);
        $customer->roles()->attach($customerRole->id);

        $requestRow = CustomerRequest::create([
            'site' => 'orkestram.net',
            'name' => 'Req 1',
            'status' => 'new',
        ]);

        $this->post('/giris', [
            'username' => 'support-user',
            'password' => 'support-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/support/requests/' . $requestRow->id . '/status', [
            'status' => 'closed',
            'internal_note' => 'Destek tarafinda kapatildi',
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_requests', [
            'id' => $requestRow->id,
            'status' => 'closed',
            'internal_note' => 'Destek tarafinda kapatildi',
        ]);

        $this->post('/cikis')->assertRedirect('/giris');

        $this->post('/giris', [
            'username' => 'customer-user',
            'password' => 'customer-pass',
        ])->assertRedirect('/hesabim');

        $this->post('/support/requests/' . $requestRow->id . '/status', [
            'status' => 'new',
        ])->assertForbidden();
    }
}

