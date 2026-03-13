<?php

namespace Tests\Feature;

use App\Models\RedirectRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        putenv('REDIRECT_RULES_LOCAL_ENABLED=true');
        $_ENV['REDIRECT_RULES_LOCAL_ENABLED'] = 'true';
        $_SERVER['REDIRECT_RULES_LOCAL_ENABLED'] = 'true';
    }

    public function test_active_redirect_rule_redirects_with_configured_code(): void
    {
        RedirectRule::create([
            'site' => 'orkestram.net',
            'old_path' => '/eski-url',
            'new_url' => '/yeni-url',
            'http_code' => 302,
            'is_active' => true,
        ]);

        $this->get('/eski-url')
            ->assertStatus(302)
            ->assertRedirect('/yeni-url');
    }

    public function test_invalid_status_code_falls_back_to_301(): void
    {
        RedirectRule::create([
            'site' => 'orkestram.net',
            'old_path' => '/yanlis-kod',
            'new_url' => '/hedef',
            'http_code' => 499,
            'is_active' => true,
        ]);

        $this->get('/yanlis-kod')
            ->assertStatus(301)
            ->assertRedirect('/hedef');
    }

    public function test_relative_self_loop_target_is_ignored(): void
    {
        RedirectRule::create([
            'site' => 'orkestram.net',
            'old_path' => '/loop',
            'new_url' => '/loop',
            'http_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/loop')->assertNotFound();
    }

    public function test_invalid_target_scheme_is_ignored(): void
    {
        RedirectRule::create([
            'site' => 'orkestram.net',
            'old_path' => '/gecersiz-hedef',
            'new_url' => 'javascript:alert(1)',
            'http_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/gecersiz-hedef')->assertNotFound();
    }

    public function test_absolute_self_loop_on_same_host_is_ignored(): void
    {
        RedirectRule::create([
            'site' => 'orkestram.net',
            'old_path' => '/mutlak-loop',
            'new_url' => 'http://localhost/mutlak-loop',
            'http_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/mutlak-loop')->assertNotFound();
    }
}
