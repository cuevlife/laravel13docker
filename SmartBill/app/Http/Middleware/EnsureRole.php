<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $minimumRole)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $requiredLevel = match ($minimumRole) {
            'admin' => User::ROLE_TENANT_ADMIN,
            'super_admin' => User::ROLE_SUPER_ADMIN,
            default => User::ROLE_USER,
        };

        if ((int) $user->role < $requiredLevel) {
            abort(403);
        }

        return $next($request);
    }
}
