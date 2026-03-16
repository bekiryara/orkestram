<?php

namespace Tests\Feature;

use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingMediaRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_renders_new_storage_media_on_detail_and_card(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/listings/render-new/cover.jpg', 'cover');
        Storage::disk('public')->put('uploads/listings/render-new/g1.jpg', 'gallery');

        Listing::query()->create([
            'site' => 'izmirorkestra.net',
            'slug' => 'render-new',
            'name' => 'Render New',
            'status' => 'published',
            'cover_image_path' => 'storage/uploads/listings/render-new/cover.jpg',
            'gallery_json' => ['storage/uploads/listings/render-new/g1.jpg'],
        ]);

        $this->withServerVariables($this->izmirHost())->get('/ilan/render-new')
            ->assertOk()
            ->assertSee('/storage/uploads/listings/render-new/cover.jpg', false)
            ->assertSee('/storage/uploads/listings/render-new/g1.jpg', false);

        $this->withServerVariables($this->izmirHost())->get('/ilanlar')
            ->assertOk()
            ->assertSee('/storage/uploads/listings/render-new/cover.jpg', false);
    }

    public function test_public_listing_renders_legacy_public_media_when_storage_copy_missing(): void
    {
        $this->prepareLegacyPublicFile('uploads/listings/render-legacy/cover.jpg');
        $this->prepareLegacyPublicFile('uploads/listings/render-legacy/g1.jpg');

        Listing::query()->create([
            'site' => 'izmirorkestra.net',
            'slug' => 'render-legacy',
            'name' => 'Render Legacy',
            'status' => 'published',
            'cover_image_path' => 'uploads/listings/render-legacy/cover.jpg',
            'gallery_json' => ['uploads/listings/render-legacy/g1.jpg'],
        ]);

        $this->withServerVariables($this->izmirHost())->get('/ilan/render-legacy')
            ->assertOk()
            ->assertSee('/uploads/listings/render-legacy/cover.jpg', false)
            ->assertSee('/uploads/listings/render-legacy/g1.jpg', false);

        $this->withServerVariables($this->izmirHost())->get('/ilanlar')
            ->assertOk()
            ->assertSee('/uploads/listings/render-legacy/cover.jpg', false);
    }

    public function test_public_listing_uses_fallback_when_media_file_missing(): void
    {
        Listing::query()->create([
            'site' => 'izmirorkestra.net',
            'slug' => 'render-missing',
            'name' => 'Render Missing',
            'status' => 'published',
            'cover_image_path' => 'storage/uploads/listings/render-missing/missing.jpg',
            'gallery_json' => ['storage/uploads/listings/render-missing/missing-gallery.jpg'],
        ]);

        $this->withServerVariables($this->izmirHost())->get('/ilan/render-missing')
            ->assertOk()
            ->assertSee('assets/listing-fallback.svg', false);

        $this->withServerVariables($this->izmirHost())->get('/ilanlar')
            ->assertOk()
            ->assertSee('assets/listing-fallback.svg', false);
    }

    private function prepareLegacyPublicFile(string $relativePath): void
    {
        $fullPath = public_path($relativePath);
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, 'legacy-file');
    }
}



