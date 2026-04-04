<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerUrl
{
    public static function usesPathMode(Request $request): bool
    {
        return WorkspaceUrl::usesPathMode($request);
    }

    public static function base(Request $request): string
    {
        if (self::usesPathMode($request)) {
            return WorkspaceUrl::centralBase($request).'/admin';
        }

        return $request->getScheme().'://admin.'.env('APP_DOMAIN', 'localhost');
    }

    public static function path(Request $request, string $path = 'dashboard'): string
    {
        $normalizedPath = trim($path, '/');

        return self::base($request).($normalizedPath !== '' ? '/'.$normalizedPath : '');
    }
}
