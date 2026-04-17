<?php

namespace App\Support;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceUrl
{
    public static function usesPathMode(Request $request): bool
    {
        // The main UX is no longer subdomain based. 
        // We prefer path-based routing (/admin/... or /workspace/...)
        return true;
    }

    public static function centralBase(Request $request): string
    {
        $host = $request->getHost();
        $scheme = $request->getScheme();
        $appDomain = trim((string) env('APP_DOMAIN', 'localhost'));

        if ($appDomain === 'localhost' && $host === 'admin.localhost') {
            return $scheme.'://localhost';
        }

        if ($appDomain !== 'localhost' && $host === 'admin.'.$appDomain) {
            return $scheme.'://'.$appDomain;
        }

        return $request->getScheme().'://'.$request->getHttpHost();
    }

    public static function centralDashboard(Request $request): string
    {
        return self::centralBase($request).'/dashboard';
    }

    public static function workspace(Request $request, Merchant|string|null $merchant, string $path = 'dashboard'): string
    {
        $projectId = self::resolveProjectId($merchant);
        $normalizedPath = trim($path, '/');
        $query = $normalizedPath !== '' && $normalizedPath !== 'dashboard'
            ? '?next='.urlencode($normalizedPath)
            : '';

        return self::centralBase($request).'/folders/open/'.$projectId.$query;
    }

    public static function current(Request $request, string $path = 'dashboard'): string
    {
        $normalizedPath = trim($path, '/');

        return self::centralBase($request).'/workspace'.($normalizedPath !== '' ? '/'.$normalizedPath : '/dashboard');
    }

    private static function resolveProjectId(Merchant|string|int|null $merchant): string
    {
        if ($merchant instanceof Merchant) {
            return (string) $merchant->getKey();
        }

        return (string) $merchant;
    }
}
