<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CdnHelper
{
    /**
     * Upload a file to Backblaze B2 and return its full CDN URL.
     *
     * @param  string  $directory  The directory inside the bucket (e.g. "products/5", "vendors/logos")
     */
    public static function upload(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'b2');

        return self::url($path);
    }

    /**
     * Build a full CDN URL from a relative storage path.
     *
     * Examples:
     *   "images/photo.jpg"  → "https://cdn.natakahii.com/images/photo.jpg"
     *   "photo.jpg"         → "https://cdn.natakahii.com/photo.jpg"
     */
    public static function url(string $path): string
    {
        $cdnBase = rtrim((string) config('app.cdn_url'), '/');

        return $cdnBase.'/'.ltrim($path, '/');
    }

    /**
     * Extract the relative storage path from a full CDN URL.
     *
     * Useful when you need to delete a file from B2 using its stored CDN URL.
     */
    public static function pathFromUrl(string $cdnUrl): string
    {
        $cdnBase = rtrim((string) config('app.cdn_url'), '/').'/';

        if (str_starts_with($cdnUrl, $cdnBase)) {
            return substr($cdnUrl, strlen($cdnBase));
        }

        return $cdnUrl;
    }

    /**
     * Ensure a stored path is a full CDN URL.
     *
     * If the value is already a full CDN URL, return as-is.
     * If it's a relative path (legacy data), prepend the CDN base.
     * Returns null for null/empty values.
     */
    public static function normalize(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return self::url($path);
    }

    /**
     * Delete a file from Backblaze B2 using its CDN URL or relative path.
     */
    public static function delete(string $cdnUrlOrPath): bool
    {
        $path = self::pathFromUrl($cdnUrlOrPath);

        return Storage::disk('b2')->delete($path);
    }
}
