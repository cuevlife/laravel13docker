<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokens
{
    public function handle(Request $request, Closure $next, int $minimum = 1)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if ((int) $user->tokens < $minimum) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient tokens. Please top up before scanning.',
            ], 402);
        }

        return $next($request);
    }
}
