<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaPath
{
    public static function listingUrl(?string $path, string $fallback = 'assets/listing-fallback.svg'): string
    {
        $publicPath = self::resolvePublicPath($path);
        return asset($publicPath ?: ltrim($fallback, '/'));
    }

    public static function avatarUrl(?string $path): string
    {
        $publicPath = self::resolvePublicPath($path);
        return $publicPath ? asset($publicPath) : '';
    }

    public static function normalizeDbPath(?string $path): ?string
    {
        $raw = self::clean($path);
        if ($raw === null) {
            return null;
        }

        if (str_starts_with($raw, 'storage/uploads/')) {
            return $raw;
        }

        if (str_starts_with($raw, 'uploads/')) {
            return 'storage/' . $raw;
        }

        if (str_starts_with($raw, 'profile-photos/')) {
            return 'storage/uploads/' . $raw;
        }

        if (str_starts_with($raw, 'storage/profile-photos/')) {
            return 'storage/uploads/' . substr($raw, strlen('storage/'));
        }

        if (str_starts_with($raw, 'storage/')) {
            return $raw;
        }

        return $raw;
    }

    public static function ensureStored(?string $path): ?string
    {
        $raw = self::clean($path);
        if ($raw === null) {
            return null;
        }

        $normalized = self::normalizeDbPath($raw);
        if ($normalized === null || !str_starts_with($normalized, 'storage/')) {
            return $normalized;
        }

        $diskRelative = substr($normalized, strlen('storage/'));
        if (Storage::disk('public')->exists($diskRelative)) {
            return $normalized;
        }

        self::migrateLegacySource($raw, $diskRelative);

        return $normalized;
    }

    public static function delete(?string $path): void
    {
        $raw = self::clean($path);
        if ($raw === null) {
            return;
        }

        $normalized = self::normalizeDbPath($raw);
        if ($normalized !== null && str_starts_with($normalized, 'storage/')) {
            $diskRelative = substr($normalized, strlen('storage/'));
            Storage::disk('public')->delete($diskRelative);

            if (file_exists(public_path($diskRelative))) {
                @unlink(public_path($diskRelative));
            }
        }

        if (str_starts_with($raw, 'uploads/') && file_exists(public_path($raw))) {
            @unlink(public_path($raw));
        }

        if (str_starts_with($raw, 'profile-photos/')) {
            Storage::disk('public')->delete($raw);
        }

        if (str_starts_with($raw, 'storage/profile-photos/')) {
            Storage::disk('public')->delete(substr($raw, strlen('storage/')));
        }
    }

    private static function resolvePublicPath(?string $path): ?string
    {
        $raw = self::clean($path);
        if ($raw === null) {
            return null;
        }

        $normalized = self::normalizeDbPath($raw);
        if ($normalized !== null && str_starts_with($normalized, 'storage/')) {
            $diskRelative = substr($normalized, strlen('storage/'));
            if (Storage::disk('public')->exists($diskRelative)) {
                return $normalized;
            }
        }

        if (str_starts_with($raw, 'uploads/') && file_exists(public_path($raw))) {
            return $raw;
        }

        if (str_starts_with($raw, 'profile-photos/') && Storage::disk('public')->exists($raw)) {
            return 'storage/' . $raw;
        }

        if (str_starts_with($raw, 'storage/profile-photos/')) {
            $legacyDiskPath = substr($raw, strlen('storage/'));
            if (Storage::disk('public')->exists($legacyDiskPath)) {
                return $raw;
            }
        }

        return null;
    }

    private static function migrateLegacySource(string $raw, string $diskRelative): void
    {
        $disk = Storage::disk('public');
        $directory = dirname($diskRelative);
        if ($directory !== '' && $directory !== '.') {
            $disk->makeDirectory($directory);
        }

        if (str_starts_with($raw, 'uploads/')) {
            $legacyPublicPath = public_path($raw);
            if (file_exists($legacyPublicPath)) {
                $stream = @fopen($legacyPublicPath, 'rb');
                if ($stream !== false) {
                    $disk->put($diskRelative, $stream);
                    fclose($stream);
                    @unlink($legacyPublicPath);
                }
            }
            return;
        }

        if (str_starts_with($raw, 'profile-photos/') && $disk->exists($raw)) {
            $disk->move($raw, $diskRelative);
            return;
        }

        if (str_starts_with($raw, 'storage/profile-photos/')) {
            $legacyDiskPath = substr($raw, strlen('storage/'));
            if ($disk->exists($legacyDiskPath)) {
                $disk->move($legacyDiskPath, $diskRelative);
            }
        }
    }

    private static function clean(?string $path): ?string
    {
        $normalized = ltrim(trim((string) ($path ?? '')), '/');
        return $normalized === '' ? null : $normalized;
    }
}
