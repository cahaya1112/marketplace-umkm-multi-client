<?php

namespace App\Support;

final class UrlGambar
{
    // Path gambar di database diubah jadi URL. File disimpan di storage/app/public (butuh: php artisan storage:link).
    public static function dari(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}