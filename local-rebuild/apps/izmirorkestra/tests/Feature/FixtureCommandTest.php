<?php

namespace Tests\Feature;

use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class FixtureCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_smoke_fixture_marks_listings_with_smoke_layer(): void
    {
        $exitCode = Artisan::call('smoke:prepare-bando-fixture', [
            '--site' => 'izmirorkestra.net',
        ]);

        $this->assertSame(0, $exitCode, Artisan::output());
        $this->assertDatabaseHas('listings', [
            'site' => 'izmirorkestra.net',
            'slug' => 'test-bando-a',
            'name' => 'TEST Bando Senaryo A',
        ]);

        $listing = Listing::query()->where('site', 'izmirorkestra.net')->where('slug', 'test-bando-a')->firstOrFail();
        $this->assertSame('smoke', $listing->meta_json['fixture_layer'] ?? null);
        $this->assertSame('smoke-bando-a', $listing->meta_json['fixture_key'] ?? null);
    }

    public function test_review_demo_fixture_uses_whitelist_slug_and_does_not_touch_smoke_listing(): void
    {
        Artisan::call('smoke:prepare-bando-fixture', [
            '--site' => 'izmirorkestra.net',
        ]);

        $smokeBefore = Listing::query()->where('site', 'izmirorkestra.net')->where('slug', 'test-bando-a')->firstOrFail();
        $smokeUpdatedAt = $smokeBefore->updated_at;

        $exitCode = Artisan::call('demo:prepare-bando-review-fixture', [
            '--site' => 'izmirorkestra.net',
        ]);

        $this->assertSame(0, $exitCode, Artisan::output());
        $this->assertDatabaseHas('listings', [
            'site' => 'izmirorkestra.net',
            'slug' => 'demo-bando-kordon-alayi',
            'name' => 'Demo Bando Kordon Alayi',
            'cover_image_path' => 'storage/uploads/listings/demo-bando-kordon-alayi/cover.jpg',
        ]);

        $demoListing = Listing::query()->where('site', 'izmirorkestra.net')->where('slug', 'demo-bando-kordon-alayi')->firstOrFail();
        $this->assertSame('review_demo', $demoListing->meta_json['fixture_layer'] ?? null);
        $this->assertSame('repo-storage', $demoListing->meta_json['fixture_media_source'] ?? null);
        $this->assertCount(4, (array) $demoListing->gallery_json);

        $smokeAfter = Listing::query()->where('site', 'izmirorkestra.net')->where('slug', 'test-bando-a')->firstOrFail();
        $this->assertSame($smokeUpdatedAt?->toAtomString(), $smokeAfter->updated_at?->toAtomString());
        $this->assertSame('smoke', $smokeAfter->meta_json['fixture_layer'] ?? null);
    }
}
