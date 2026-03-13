<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountManagementTest extends TestCase
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

    public function test_user_can_update_profile_and_password_from_hesabim(): void
    {
        $role = Role::create(['slug' => 'customer', 'name' => 'Customer', 'is_active' => true]);
        $user = User::create([
            'name' => 'A',
            'username' => 'profile-user',
            'email' => 'profile-user@example.test',
            'password' => Hash::make('old-pass-123'),
            'is_active' => true,
        ]);
        $user->roles()->attach($role->id);

        $this->post('/giris', [
            'username' => 'profile-user',
            'password' => 'old-pass-123',
        ])->assertRedirect('/hesabim');

        $this->post('/hesabim/profil', [
            'name' => 'Profile User Updated',
            'username' => 'profile-user-upd',
            'email' => 'profile-user-upd@example.test',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'profile-user-upd',
            'email' => 'profile-user-upd@example.test',
            'name' => 'Profile User Updated',
        ]);

        $this->post('/hesabim/sifre', [
            'current_password' => 'old-pass-123',
            'new_password' => 'new-pass-123',
            'new_password_confirmation' => 'new-pass-123',
        ])->assertRedirect();

        $fresh = User::query()->findOrFail($user->id);
        $this->assertTrue(Hash::check('new-pass-123', (string) $fresh->password));
    }
}

