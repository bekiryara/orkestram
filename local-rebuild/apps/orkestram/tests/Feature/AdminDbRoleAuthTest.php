<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDbRoleAuthTest extends TestCase
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

    public function test_user_with_content_editor_role_can_access_allowed_admin_pages(): void
    {
        $role = Role::create([
            'slug' => 'content_editor',
            'name' => 'Content Editor',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Editor User',
            'username' => 'editor-db',
            'email' => 'editor@example.test',
            'password' => Hash::make('secret-pass'),
            'is_active' => true,
        ]);
        $user->roles()->attach($role->id);

        $headers = [
            'PHP_AUTH_USER' => 'editor-db',
            'PHP_AUTH_PW' => 'secret-pass',
        ];

        $this->withServerVariables($headers)->get('/admin/pages')->assertOk();
        $this->withServerVariables($headers)->get('/admin/listings')->assertOk();
        $this->withServerVariables($headers)->get('/admin/city-pages')->assertForbidden();
    }
}
