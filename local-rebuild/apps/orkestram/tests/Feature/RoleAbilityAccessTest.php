<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAbilityAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $accounts = [
            ['user' => 'editor', 'pass' => 'editor-pass', 'role' => 'content_editor'],
            ['user' => 'viewer', 'pass' => 'viewer-pass', 'role' => 'viewer'],
        ];

        $json = json_encode($accounts);
        putenv('ADMIN_ACCOUNTS_JSON=' . $json);
        $_ENV['ADMIN_ACCOUNTS_JSON'] = $json;
        $_SERVER['ADMIN_ACCOUNTS_JSON'] = $json;
    }

    public function test_content_editor_can_access_pages_and_listings_but_not_city_pages(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'editor',
            'PHP_AUTH_PW' => 'editor-pass',
        ];

        $this->withServerVariables($headers)->get('/admin/pages')->assertOk();
        $this->withServerVariables($headers)->get('/admin/listings')->assertOk();
        $this->withServerVariables($headers)->get('/admin/city-pages')->assertForbidden();
    }

    public function test_viewer_has_admin_access_but_no_crud_permissions(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'viewer',
            'PHP_AUTH_PW' => 'viewer-pass',
        ];

        $this->withServerVariables($headers)->get('/admin/pages')->assertForbidden();
        $this->withServerVariables($headers)->get('/admin/listings')->assertForbidden();
        $this->withServerVariables($headers)->get('/admin/city-pages')->assertForbidden();
    }
}
