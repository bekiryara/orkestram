<?php

namespace App\Services\Listings;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ListingMediaService
{
    public function apply(Request $request, array $data, ?Listing $listing = null): array
    {
        $slug = $data['slug'];
        $existingCover = $listing?->cover_image_path;
        $existingGallery = is_array($listing?->gallery_json) ? $listing->gallery_json : [];

        if ($request->input('remove_cover_image') === '1') {
            $existingCover = null;
        }
        if ($request->input('reset_gallery') === '1') {
            $existingGallery = [];
        }

        $removeGallery = array_values(array_filter((array) $request->input('remove_gallery', [])));
        if ($removeGallery) {
            $existingGallery = array_values(array_filter(
                $existingGallery,
                fn($img) => !in_array($img, $removeGallery, true)
            ));
        }

        $orderedGallery = $this->parseGalleryOrder($request->input('gallery_order'));
        if ($orderedGallery) {
            $orderMap = array_flip($orderedGallery);
            usort($existingGallery, function (string $a, string $b) use ($orderMap): int {
                $va = $orderMap[$a] ?? PHP_INT_MAX;
                $vb = $orderMap[$b] ?? PHP_INT_MAX;
                return $va <=> $vb;
            });
        }

        if ($request->hasFile('cover_image')) {
            $coverPath = $this->moveUpload($request->file('cover_image'), $slug);
            $existingCover = $coverPath;
        }

        if ($request->hasFile('gallery_images')) {
            foreach ((array) $request->file('gallery_images') as $file) {
                if ($file instanceof UploadedFile) {
                    $existingGallery[] = $this->moveUpload($file, $slug);
                }
            }
        }

        $data['cover_image_path'] = $existingCover;
        $data['gallery_json'] = array_values(array_unique(array_filter($existingGallery)));

        return $data;
    }

    private function moveUpload(UploadedFile $file, string $slug): string
    {
        $safeSlug = Str::slug($slug ?: 'listing');
        $relativeDir = 'uploads/listings/' . $safeSlug;
        $disk = Storage::disk('public');

        if (!$disk->exists($relativeDir) && !$disk->makeDirectory($relativeDir)) {
            throw ValidationException::withMessages([
                'cover_image' => 'Gorsel klasoru olusturulamadi. Dizin izinlerini kontrol edin.',
            ]);
        }

        $filename = now()->format('YmdHis') . '-' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
        $storedPath = $file->storeAs($relativeDir, $filename, 'public');
        if ($storedPath === false) {
            throw ValidationException::withMessages([
                'cover_image' => 'Gorsel yuklenemedi. Storage/public izinlerini kontrol edin.',
            ]);
        }

        return 'storage/' . ltrim($storedPath, '/');
    }

    private function parseGalleryOrder(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn($v) => is_string($v) ? trim($v) : '',
            $decoded
        )));
    }
}

