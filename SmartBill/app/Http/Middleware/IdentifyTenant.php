<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $merchantParameter = $request->route('folder') ?? $request->route('project') ?? $request->route('workspace') ?? $request->route('merchant');
        $subdomain = $request->route('subdomain');

        if ($merchantParameter instanceof Merchant) {
            $merchant = $merchantParameter;
        } elseif (!$merchantParameter) {
            $activeFolderId = (int) $request->session()->get('active_folder_id');

            if (!$activeFolderId) {
                return redirect()->route('dashboard');
            }

            $merchant = Merchant::query()->findOrFail($activeFolderId);
        } else {
            $lookupSubdomain = $subdomain ?: $merchantParameter;

            if (!$lookupSubdomain) {
                abort(404);
            }

            $merchant = Merchant::query()
                ->where(function ($query) use ($lookupSubdomain) {
                    if (is_numeric($lookupSubdomain)) {
                        $query->whereKey((int) $lookupSubdomain);
                    }

                    $query->orWhere('subdomain', $lookupSubdomain);
                })
                ->firstOrFail();
        }

        if (!$merchant->isActive() && !(Auth::check() && Auth::user()->isSuperAdmin())) {
            abort(423, 'This workspace is archived and currently unavailable.');
        }

        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is owner or has a membership record
            $hasAccess = $user->isSuperAdmin()
                || $user->merchants()->where('merchant_id', $merchant->id)->exists()
                || (int) $merchant->user_id === (int) $user->id;

            if (!$hasAccess) {
                abort(403, 'You do not have permission to access this folder.');
            }
        }

        app()->instance('tenant', $merchant);
        view()->share('activeTenant', $merchant);

        if ($request->route()) {
            $request->route()->forgetParameter('subdomain');
            $request->route()->forgetParameter('folder');
            $request->route()->forgetParameter('project');
            $request->route()->forgetParameter('workspace');
            $request->route()->forgetParameter('merchant');
        }

        return $next($request);
    }
}
