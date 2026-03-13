<?php

namespace Tests\Feature;

use App\Models\CityPage;
use App\Models\Listing;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAndSeoRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_public_and_seo_routes_work(): void
    {
        Page::create([
            'site' => 'orkestram.net',
            'slug' => 'kurumsal-test',
            'title' => 'Kurumsal Test',
            'template' => 'page',
            'status' => 'published',
        ]);

        Listing::create([
            'site' => 'orkestram.net',
            'slug' => 'listing-test',
            'name' => 'Listing Test',
            'status' => 'published',
        ]);

        CityPage::create([
            'site' => 'orkestram.net',
            'slug' => 'sehir-test',
            'city' => 'Izmir',
            'title' => 'Sehir Test',
            'status' => 'published',
        ]);

        $this->get('/')->assertOk();
        $this->get('/ilan/listing-test')->assertOk();
        $this->get('/sehir/sehir-test')->assertOk();
        $this->get('/sayfa/kurumsal-test')->assertOk();

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    }
}
