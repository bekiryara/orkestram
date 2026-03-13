<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_requires_basic_auth(): void
    {
        $this->get('/admin/pages')->assertStatus(401);
    }

    public function test_admin_pages_is_accessible_with_valid_basic_auth(): void
    {
        $headers = [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'change-me',
        ];

        $this->withServerVariables($headers)
            ->get('/admin/pages')
            ->assertOk();
    }
}
