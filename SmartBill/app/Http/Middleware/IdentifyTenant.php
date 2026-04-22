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

        if ($merchantParameter instanceof Merchant) {
            $merchant = $merchantParameter;
        } elseif (!$merchantParameter) {
            $activeFolderId = (int) $request->session()->get('active_folder_id');

            if (!$activeFolderId) {
                return redirect()->route('dashboard');
            }

            $merchant = Merchant::query()->findOrFail($activeFolderId);
        } else {
            $lookupId = $merchantParameter;

            if (!$lookupId) {
                abort(404);
            }

            $merchant = Merchant::query()
                ->where(function ($query) use ($lookupId) {
                    if (is_numeric($lookupId)) {
                        $query->whereKey((int) $lookupId);
                    }
                })
                ->firstOrFail();
        }

        if (!$merchant->isActive() && !(Auth::check() && Auth::user()->isSuperAdmin())) {
            abort(423, 'This workspace is archived and currently unavailable.');
        }

        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is owner or superadmin
            $hasAccess = $user->isSuperAdmin() || (int) $merchant->user_id === (int) $user->id;

            if (!$hasAccess) {
                abort(403, 'You do not have permission to access this folder.');
            }
        }

        app()->instance('tenant', $merchant);
        view()->share('activeTenant', $merchant);

        if ($request->route()) {
            $request->route()->forgetParameter('folder');
            $request->route()->forgetParameter('project');
            $request->route()->forgetParameter('workspace');
            $request->route()->forgetParameter('merchant');
        }

        return $next($request);
    }
}
