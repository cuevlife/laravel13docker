<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentralController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $stores = $user->accessibleMerchants()
            ->with([
                'users' => fn ($query) => $query->where('users.id', $user->id),
            ])
            ->get();

        return view('admin.central-dashboard', compact('stores'));
    }

    public function openProject(Request $request, Merchant $project)
    {
        $user = Auth::user();

        $hasAccess = $user->isSuperAdmin()
            || $user->merchants()->where('merchant_id', $project->id)->exists()
            || (int) $project->user_id === (int) $user->id;

        abort_unless($hasAccess, 403);
        abort_if(!$project->isActive() && !$user->isSuperAdmin(), 423, 'This workspace is archived and currently unavailable.');

        $request->session()->put('active_project_id', $project->id);

        $next = trim((string) $request->query('next', 'slips'), '/');

        $target = match ($next) {
            'templates' => route('workspace.templates.index', absolute: false),
            default => route('workspace.slip.index', absolute: false),
        };

        return redirect()->to($target);
    }
}