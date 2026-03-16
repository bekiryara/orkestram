<?php

namespace App\Services\Listings;

use App\Models\Listing;
use App\Support\MediaPath;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ListingMediaService
{
    public function apply(Request $request, array $data, ?Listing $listing = null): array
    {
        $slug = (string) ($data['slug'] ?? $listing?->slug ?? 'listing');
        $existingCover = MediaPath::ensureStored($listing?->cover_image_path);
        $existingGallery = $this->normalizeGallery($listing?->gallery_json);
        $pathsToDelete = [];

        if ($request->input('remove_cover_image') === '1') {
            if ($existingCover !== null) {
                $pathsToDelete[] = $existingCover;
            }
            $existingCover = null;
        }

        if ($request->input('reset_gallery') === '1') {
            $pathsToDelete = array_merge($pathsToDelete, $existingGallery);
            $existingGallery = [];
        }

        $removeGallery = array_values(array_filter(array_map(
            static fn ($img) => MediaPath::normalizeDbPath(is_string($img) ? $img : null),
            (array) $request->input('remove_gallery', [])
        )));
        if ($removeGallery) {
            $kept = [];
            foreach ($existingGallery as $img) {
                if (in_array($img, $removeGallery, true)) {
                    $pathsToDelete[] = $img;
                    continue;
                }
                $kept[] = $img;
            }
            $existingGallery = array_values($kept);
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
            $coverPath = $this->storeUpload($request->file('cover_image'), $slug);
            if ($existingCover !== null && $existingCover !== $coverPath) {
                $pathsToDelete[] = $existingCover;
            }
            $existingCover = $coverPath;
        }

        if ($request->hasFile('gallery_images')) {
            foreach ((array) $request->file('gallery_images') as $file) {
                if ($file instanceof UploadedFile) {
                    $existingGallery[] = $this->storeUpload($file, $slug);
                }
            }
        }

        $data['cover_image_path'] = $existingCover;
        $data['gallery_json'] = array_values(array_unique(array_filter($existingGallery)));

        $this->deletePaths($pathsToDelete);

        return $data;
    }

    private function storeUpload(UploadedFile $file, string $slug): string
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

    private function normalizeGallery(mixed $gallery): array
    {
        if (!is_array($gallery)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($img) => MediaPath::ensureStored(is_string($img) ? $img : null),
            $gallery
        )));
    }

    private function deletePaths(array $paths): void
    {
        foreach (array_values(array_unique(array_filter($paths))) as $path) {
            MediaPath::delete($path);
        }
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
            static fn ($v) => MediaPath::normalizeDbPath(is_string($v) ? trim($v) : null),
            $decoded
        )));
    }
}
